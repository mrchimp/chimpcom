<?php

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Tag as TagModel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Tag extends Command
{
    protected function configure()
    {
        $this->setName('tag');
        $this->setDescription('View tags');
        $this->addArgument('tag', InputArgument::OPTIONAL, 'List memories for a given tag');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('tag')) {
            return $this->showTag($input, $output);
        }

        return $this->listTags($input, $output);
    }

    public function showTag(InputInterface $input, OutputInterface $output)
    {
        $tag = TagModel::where('tag', $input->getArgument('tag'))->first();

        if (!$tag) {
            $output->error('Tag not found.');
            return 1;
        }

        $output->write(Format::memories($tag->memories));

        return 0;
    }

    public function listTags(InputInterface $input, OutputInterface $output)
    {
        $tag_names = TagModel::pluck('tag')->values()->toArray();

        $output->write(Format::listToTable($tag_names, 4, true));

        return 0;
    }
}
