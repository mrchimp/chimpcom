<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Log as ErrorLog;
use Mrchimp\Chimpcom\Console\Output;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Log;
use Mrchimp\Chimpcom\Models\Alias as ChimpcomAlias;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends SymfonyCommand
{
    protected $relatedCommands = [];

    protected $log;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->log = new Log;
    }

    public static function make(string $name, string $type = 'command'): ?Command
    {
        if ($type === 'command') {
            $config_str = 'chimpcom.commands.' . strtolower($name);
        } elseif ($type === 'action') {
            $config_str = 'chimpcom.actions.' . strtolower($name);
        } else {
            throw new \Exception('Invalid command type');
        }

        $command_class = config($config_str);

        if (!class_exists($command_class)) {
            return null;
        }

        return new $command_class;
    }

    public static function exists(string $name = null)
    {
        $class = config('chimpcom.commands.' . strtolower($name));

        if (!$class) {
            return false;
        }

        if (!class_exists($class)) {
            return false;
        }

        return true;
    }

    /**
     * Generate a help string from the provided parts.
     *
     * @return String
     */
    protected function generateHelp()
    {
        $out = '';

        $out .= Format::title(strtoupper('NAME')) . '<br><br>';
        $out .= '&nbsp;&nbsp;' . $this->getName() . ' - '. $this->getDescription() . '<br><br>';

        if ($this->getHelp()) {
            $out .= '&nbsp;&nbsp;' . e($this->getHelp()) . '<br><br>';
        }

        $out .= Format::title('SYNTAX') . '<br><br>';
        $out .= '&nbsp;&nbsp;' . e($this->getSynopsis('long', true)) . '<br><br>';

        if (count($this->getUsages())) {
            $out .= Format::title('USAGE') . '<br><br>';

            foreach ($this->getUsages() as $usage) {
                $out .= '&nbsp;&nbsp;<code>' . $usage . '</code><br>';
            }

            $out .= '<br>';
        }

        if ($this->getRelated()) {
            $out .= Format::title('SEE ALSO') . '<br><br>';
            $out .= '&nbsp;&nbsp;' . implode(', ', $this->getRelated()) . '<br><br>';
        }

        if (count($this->getAliases())) {
            $out .= Format::title('ALIASES') . '<br><br>';
            $out .= '&nbsp;&nbsp;' . implode(', ', $this->getAliases()) . '<br><br>';
        }

        $definition = $this->getDefinition();

        if (count($definition->getArguments())) {
            $out .= Format::title('ARGUMENTS') . '<br><br>';

            foreach ($definition->getArguments() as $argument) {
                $out .= '&nbsp;&nbsp;<strong>' . $argument->getName() . '</strong><br>';
                $out .= '&nbsp;&nbsp;' . $argument->getDescription() . '<br><br>';
            }
        }

        if (count($definition->getOptions())) {
            $out .= Format::title('OPTIONS') . '<br><br>';

            foreach ($definition->getOptions() as $option) {
                $out .= '&nbsp;&nbsp;<strong>';
                $out .= '--' . $option->getName();
                if ($option->getShortcut()) {
                    $out .= ' / -' . $option->getShortcut();
                }
                $out .= '</strong><br>';
                $out .= '&nbsp;&nbsp;' . $option->getDescription() . '<br>';
            }
        }

        return $out;
    }

    /**
     * Returns the aliases for the command.
     *
     * @return array An array of aliases for the command
     */
    public function getAliases(): array
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
    public function run(InputInterface $input, OutputInterface $output): int
    {
        parent::run($input, $output);

        $this->doLog($input);

        return 0;
    }

    protected function doLog(InputInterface $input)
    {
        $this->log->info($this->getName() . ' ' . (string) $input);
    }
}
