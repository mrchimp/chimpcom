<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Models\Memory;
use Mrchimp\Chimpcom\Models\Tag;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create a memory item
 */
class Save extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('save');
        $this->setDescription(
            'Save a memory. Each memory consists of a name and some content. The name must be a single word and does NOT have to be unique. The description can be a word, a sentence, a URL or whatever.' . Format::nl(2) .
                'Once the memory has been saved you can search for it using FIND or SHOW and delete it with FORGET command.' . Format::nl(2) .
                'You may need to encase your description in quotes in order for it to pass as expected.' . Format::nl(2) .
                'By default the memory will only be visible by you. To make it visible to other users add the --public flag or after saving, use the SETPUBLIC command.' . Format::nl(2) .
                'You can also associate a memory with a project by using the --project option.' . Format::nl(2) .
                'You can add tags to your memory by using the @ symbol. For example: save name @tag1 @tag2 content'
        );
        $this->addUsage('chimpcom A command line website.');
        $this->addRelated('forget');
        $this->addRelated('show');
        $this->addRelated('find');
        $this->addRelated('setpublic');

        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'The name of the memory to save. Must be a single word. This does <em>not</em> need to be unique.'
        );

        $this->addArgument(
            'content',
            InputArgument::IS_ARRAY | InputArgument::REQUIRED,
            'The content of the memory.'
        );

        $this->addOption(
            'public',
            null,
            null,
            'Sets the memory as publicly visible. Otherwise only you will be able to see it.'
        );

        $this->addOption(
            'project',
            'p',
            InputOption::VALUE_REQUIRED,
            "Associate this memory with a project."
        );
    }

    /**
     * Run the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));

            return 1;
        }

        $user = Auth::user();

        $name = $input->getArgument('name');
        $content = implode(' ', $input->getArgument('content'));
        $is_public = $input->getOption('public');
        $project_name = $input->getOption('project');
        $project = null;

        $tags = Tag::fromString($content);
        $content = Tag::stripTagsFromString($content);

        if ($project_name) {
            $project = Auth::user()
                ->projects()
                ->nameOrId($project_name)
                ->first();

            if (!$project) {
                $output->error('Project not found.');
                return 3;
            }
        }

        $output->write('Name: ' . Format::escape($name) . Format::nl());
        $output->write('Content: ' . Format::escape($content) . Format::nl());

        if (!empty($tags)) {
            $output->write('Tags: ' . implode(', ', $tags) . Format::nl());
        }

        $memory = new Memory();
        $memory->name = $name;
        $memory->content = $content;
        $memory->user_id = $user->id;
        $memory->public = $is_public;
        $memory->project_id = $project ? $project->id : null;

        if (!$memory->save()) {
            $output->error('Could not save memory. Try again.');
            return 2;
        }

        foreach ($tags as $tag_name) {
            $tag = Tag::firstOrCreate([
                'tag' => $tag_name,
            ]);
            $memory->tags()->save($tag);
        }

        $output->alert('Memory saved. Id: ' . $memory->id);

        return 0;
    }
}
