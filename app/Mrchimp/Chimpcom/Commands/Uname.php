<?php

/**
 * How some fake server info
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Facades\Chimpcom;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * How some fake server info
 */
class Uname extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('uname');
        $this->setDescription('Print system information.');
        $this->addOption(
            'kernel-name',
            's',
            null,
            'Print the kernel name.'
        );
        $this->addOption(
            'nodename',
            'n',
            null,
            'Print the network node hostname.'
        );
        $this->addOption(
            'kernel-version',
            'v',
            null,
            'Print the kernel version.'
        );
        $this->addOption(
            'kernel-release',
            'r',
            null,
            'Print the kernel release.'
        );
        $this->addOption(
            'machine',
            'm',
            null,
            'Print the machine hardware name.'
        );
        $this->addOption(
            'processor',
            'p',
            null,
            'Print the processor type (non-portable).'
        );
        $this->addOption(
            'hardware-platform',
            'i',
            null,
            'Print the hardware platform (non-portable).'
        );
        $this->addOption(
            'operating-system',
            'o',
            null,
            'Print the operating system.'
        );
        $this->addOption(
            'all',
            'a',
            null,
            'Print all information.'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $flags = [
            's' => 'kernel-name',
            'n' => 'nodename',
            'r' => 'kernel-version',
            'v' => 'kernel-release',
            'm' => 'machine',
            'p' => 'processor',
            'i' => 'hardware-platform',
            'o' => 'operating-system'
        ];

        $bits = [
            's' => 'Chimpcom',
            'n' => $_SERVER['HTTP_HOST'],
            'r' => Chimpcom::getVersion(),
            'v' => date('d M Y H:i:s'),
            'p' => 'unknown',
            'i' => 'unknown',
            'm' => 'unknown',
            'o' => 'Interwebs'
        ];

        if ($input->getOption('all')) {
            $output->write(implode(' ', $bits));

            return 1;
        }

        foreach ($flags as $key => $flag) {
            if ($input->getOption($flag)) {
                $output->write($key . '<br>');
                $output->write($bits[$key]);
                return true;
            }
        }

        $output->write($bits['s']);

        return 0;
    }
}
