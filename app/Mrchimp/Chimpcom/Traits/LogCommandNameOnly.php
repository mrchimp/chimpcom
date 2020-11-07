<?php

namespace Mrchimp\Chimpcom\Traits;

use Symfony\Component\Console\Input\InputInterface;

trait LogCommandNameOnly
{
    protected function doLog(InputInterface $input)
    {
        $this->log->info($this->getName());
    }
}
