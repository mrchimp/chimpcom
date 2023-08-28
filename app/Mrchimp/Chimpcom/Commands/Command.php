<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Log as ErrorLog;
use Illuminate\Support\Facades\Validator;
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

        $out .= Format::title(strtoupper('NAME')) . Format::nl(2);
        $out .= Format::nbsp(2) . $this->getName() . ' - ' . $this->getDescription() . Format::nl(2);

        if ($this->getHelp()) {
            $out .= Format::nbsp(2) . Format::escape($this->getHelp()) . Format::nl(2);
        }

        $out .= Format::title('SYNTAX') . Format::nl(2);
        $out .= Format::nbsp(2) . Format::escape($this->getSynopsis('long', true)) . Format::nl(2);

        if (count($this->getUsages())) {
            $out .= Format::title('USAGE') . Format::nl(2);

            foreach ($this->getUsages() as $usage) {
                $out .= Format::nbsp(2) . Format::code($usage) . Format::nl();
            }

            $out .= Format::nl();
        }

        if ($this->getRelated()) {
            $out .= Format::title('SEE ALSO') . Format::nl(2);
            $out .= Format::nbsp(2) . implode(', ', $this->getRelated()) . Format::nl(2);
        }

        if (count($this->getAliases())) {
            $out .= Format::title('ALIASES') . Format::nl(2);
            $out .= Format::nbsp(2) . implode(', ', $this->getAliases()) . Format::nl(2);
        }

        $definition = $this->getDefinition();

        if (count($definition->getArguments())) {
            $out .= Format::title('ARGUMENTS') . Format::nl(2);

            foreach ($definition->getArguments() as $argument) {
                $out .= Format::nbsp(2) . Format::bold($argument->getName()) . Format::nl();
                $out .= Format::nbsp(2) . $argument->getDescription() . Format::nl(2);
            }
        }

        if (count($definition->getOptions())) {
            $out .= Format::title('OPTIONS') . Format::nl(2);

            foreach ($definition->getOptions() as $option) {
                $out .= Format::nbsp(2);
                $option_str = '--' . $option->getName();
                if ($option->getShortcut()) {
                    $option_str .= ' / -' . $option->getShortcut();
                }
                $out .= Format::bold($option_str) . Format::nl();
                $out .= Format::nbsp(2) . $option->getDescription() . Format::nl();
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

    /**
     * Get the rules for validating arguments and options
     */
    protected function rules(InputInterface $input): array
    {
        return [];
    }

    /**
     * Get the message for validating arguments and options
     */
    protected function messages(InputInterface $input): array
    {
        return [];
    }

    /**
     * Validate the arguments and options and returns any errors found.
     *
     * Most validation can be done simply in the command configure() method
     * but this allows us to add rules e.g. per-subcommand.
     *
     * Return false on success, MessageBag on failure.
     */
    protected function validate(InputInterface $input): bool | MessageBag
    {
        // dd([
        //     ...$input->getArguments(),
        //     ...$input->getOptions(),
        // ]);
        $validator = Validator::make(
            [
                ...$input->getArguments(),
                ...$input->getOptions(),
            ],
            $this->rules($input),
            $this->messages($input)
        );

        if ($validator->fails()) {
            return $validator->errors();
        }

        return true;
    }
}
