<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Str;
use Mrchimp\Chimpcom\Chimpcom;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Alias;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Read the manualChimpcom
 */
class Man extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('man');
        $this->setDescription('Gets help on a given command. Use --commands to get a list of available commands.');
        $this->addUsage('man [&lt;command_name&gt;|--commands|-c]');
        $this->addUsage('man projects');

        $this->addOption(
            'commands',
            'c',
            null,
            'Lists all commands'
        );

        $this->addArgument(
            'command_name',
            null,
            'Command to look up help for.'
        );
    }

    /**
     * Run the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('commands')) {
            $commands = Chimpcom::getCommandList();
            $output->write(Format::listToTable($commands, 3, true));

            return 0;
        }

        if (!$input->getArgument('command_name')) {
            $output->write('This is how you get help. Type <code>man man</code> for more help on the help.');

            return 0;
        }

        $page_name = Alias::lookup($input->getArgument('command_name'));
        $command = Chimpcom::instantiateCommand($page_name);

        if (!$command) {
            $output->write(Format::error('No man page found'));

            return 1;
        }

        $text = $command->generateHelp();
        $output->write($text);

        return 0;
    }


    /**
     * Return tab completion options for the current command input
     *
     * @param  Input  $input
     * @return string
     */
    public function tab(InputInterface $input)
    {
        $command_name = $input->getArgument('command_name');
        $commands = collect(Chimpcom::getCommandList());

        return $commands
            ->filter(function ($name) use ($command_name) {
                return Str::startsWith($name, $command_name);
            })
            ->transform(function ($item) {
                return 'man ' . $item;
            })
            ->values();
    }
}
