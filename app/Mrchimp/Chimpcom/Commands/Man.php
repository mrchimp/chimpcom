<?php
/**
 * Read the manual
 */

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Mrchimp\Chimpcom\Chimpcom;
use Mrchimp\Chimpcom\ChimpcomAlias;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Man as ManPage;
use Mrchimp\Chimpcom\Models\Alias;

/**
 * Read the manualChimpcom
 */
class Man extends Command
{

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
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('commands')) {
            $commands = Chimpcom::getCommandList();
            $output->write(Format::listToTable($commands, 3, true));
            return;
        }

        if (!$input->getArgument('command_name')) {
            $output->write('This is how you get help. Type <code>man man</code> for more help on the help.');
            return;
        }

        $page_name = Alias::lookup($input->getArgument('command_name'));
        // $page_name = Input::getAlias($this->input->get(1));
        $command = Chimpcom::getCommand($page_name);

        if (!$command) {
          $output->write(Format::error('No man page found'));
          return;
        }

        $text = $command->generateHelp();
        $output->write($text);
        return;
    }

}
