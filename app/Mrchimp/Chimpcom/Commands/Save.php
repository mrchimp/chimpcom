<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Models\Memory;
use Mrchimp\Chimpcom\Models\Tag;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
        $this->setDescription(<<<DESC
Save a memory. Each memory consists of a name and some content. The name must be a
single word and does NOT have to be unique. The description can be a word, a
sentence, a URL or whatever.<br><br>
Once the memory has been saved you can search for it using FIND or SHOW and
delete it with FORGET command.<br><br>
You may need to encase your description in quotes in order for it to pass as expected.<br><br>
By default the memory will only be visible by you.
To make it visible to other users add the --public or -p flag or after saving,
use the SETPUBLIC command.
DESC);
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
            'p',
            null,
            'Sets the memory as publicly visible. Otherwise only you will be able to see it.'
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

        $name      = $input->getArgument('name');
        $content   = implode(' ', $input->getArgument('content'));
        $is_public = $input->getOption('public');

        $output->write('Name: ' . e($name) . '<br>');
        $output->write('Content: ' . e($content) . '<br>');

        $memory = new Memory();
        $memory->name    = $name;
        $memory->content = $content;
        $memory->user_id = $user->id;
        $memory->public  = $is_public;

        if (!$memory->save()) {
            $output->error('Could not save memory. Try again.');
            return 2;
        }

        $tags = Tag::fromString($content);

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
