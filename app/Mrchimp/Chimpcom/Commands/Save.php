<?php
/**
 * Create a memory item
 */

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Auth;
use Mrchimp\Chimpcom\Models\Memory;
use Mrchimp\Chimpcom\Models\Tag;
use Mrchimp\Chimpcom\Format;

/**
 * Create a memory item
 */
class Save extends Command
{

    public function configure()
    {
        $this->setName('save');
        $this->setDescription('Save a memory. Each memory consists of a name a description. The name must be a single word. The description can be a whole paragraph. By default the memory will only be visible by you. To make it visible to other users add the --public or -p flag or after saving, use the SETPUBLIC command.<br><br>Once the memory has been saved you can search for it using FIND or SHOW and delete it with FORGET.');
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
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // $num_params = count($this->input->getParamArray());
        //
        // if ($num_params < 2) {
        //   $this->response->error('Gonna need more than that.');
        //   return false;
        // }

        // $content = '';
        // $name    = $this->input->get(1);
        // $content = implode(' ', array_slice($this->input->getParamArray(), 1));

        if (!Auth::check()) {
            $output->write(Format::error('You must log in to use this command.'));
            return;
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
        }

        // @todo get tags working
        // foreach ($this->input->getTags() as $tag_word) {
        //   $tag = new Tag();
        //   $tag->tag = $tag_word;
        //   $memory->tags()->save($tag);
        // }

        $output->alert('Memory saved. Id: ' . $memory->id);
    }

}
