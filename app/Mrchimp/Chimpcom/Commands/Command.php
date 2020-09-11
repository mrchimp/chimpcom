<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Log as ErrorLog;
use Mrchimp\Chimpcom\Console\Output;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Log;
use Mrchimp\Chimpcom\Models\Alias as ChimpcomAlias;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends SymfonyCommand
{
    // @todo fix per-command logging
    protected $log_this = true;

    protected $relatedCommands = [];

    protected $log;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->log = new Log;
    }

    /**
     * Generate a help string from the provided parts.
     *
     * @return String
     */
    protected function generateHelp()
    {
        $out = '';

        $out .= Format::title($this->getName()) . '<br>';
        $out .= $this->getDescription() . '<br><br>';

        if ($this->getHelp()) {
            $out .= e($this->getHelp()) . '<br><br>';
        }

        $out .= Format::alert('Syntax') . '<br>';
        $out .= e($this->getSynopsis('long', true)) . '<br><br>';

        if (count($this->getUsages())) {
            $out .= Format::alert('Usage') . '<br>';

            foreach ($this->getUsages() as $usage) {
                $out .= '<code>' . $usage . '</code><br>';
            }

            $out .= '<br>';
        }

        if ($this->getRelated()) {
            $out .= Format::alert('See Also') . '<br>';
            $out .= implode(', ', $this->getRelated()) . '<br><br>';
        }

        if (count($this->getAliases())) {
            $out .= Format::alert('Aliases') . '<br>';
            $out .= implode(', ', $this->getAliases()) . '<br><br>';
        }

        $definition = $this->getDefinition();

        if (count($definition->getArguments())) {
            $out .= Format::alert('Arguments') . '<br>';

            foreach ($definition->getArguments() as $argument) {
                $out .= '<strong>' . $argument->getName() . '</strong><br>';
                $out .= $argument->getDescription() . '<br><br>';
            }
        }

        if (count($definition->getOptions())) {
            $out .= Format::alert('Options') . '<br>';

            foreach ($definition->getOptions() as $option) {
                $out .= '<strong>';
                    $out .= '--' . $option->getName();
                    if ($option->getShortcut()) {
                        $out .= ' / -' . $option->getShortcut();
                    }
                $out .= '</strong><br>';
                $out .= $option->getDescription() . '<br>';
            }
        }

        return $out;
    }

    /**
     * Returns the aliases for the command.
     *
     * @return array An array of aliases for the command
     */
    public function getAliases()
    {
        return ChimpcomAlias::where('alias', $this->getName())
                            ->pluck('name')
                            ->toArray();
    }

    /**
     * Add the name of a related command for use in man pages.
     *
     * @param String $name The name of the related command
     */
    public function addRelated($name)
    {
        $this->relatedCommands[] = $name;
    }

    /**
     * Get related commands
     *
     * @return Array An array of command names
     */
    public function getRelated()
    {
        return $this->relatedCommands;
    }

    /**
     * Return tab completion options for the current command input
     *
     * @return string
     */
    public function tabComplete(InputInterface $input)
    {
        // force the creation of the synopsis before the merge with the app definition
        $this->getSynopsis(true);
        $this->getSynopsis(false);

        // add the application arguments and options
        $this->mergeApplicationDefinition();

        // bind the input against the command specific arguments/options
        try {
            $input->bind($this->getDefinition());
        } catch (ExceptionInterface $e) {
            ErrorLog::error($e);
            return response()->json([], 500);
        }

        $this->initialize($input, new Output);

        return $this->tab($input);
    }

    /**
     * Run tab complete logic
     *
     * @return array
     */
    public function tab(InputInterface $input)
    {
        return [];
    }

    /**
     * Runs the command.
     *
     * The code to execute is either defined directly with the
     * setCode() method or by overriding the execute() method
     * in a sub-class.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return int The command exit code
     *
     * @see setCode()
     * @see execute()
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        parent::run($input, $output);

        $this->log->info($this->getName());
    }
}
