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
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->listTags($input, $output);
    }

    public function listTags(InputInterface $input, OutputInterface $output)
    {
        $tag_names = TagModel::pluck('tag')->values()->toArray();

        $output->write(Format::listToTable($tag_names, 4, true));

        return 0;
    }
}
