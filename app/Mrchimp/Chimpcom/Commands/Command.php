<?php

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mrchimp\Chimpcom\Models\Alias as ChimpcomAlias;
use Mrchimp\Chimpcom\Format;

class Command extends SymfonyCommand
{

    protected $relatedCommands = [];

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

        $out .= Format::alert('Syntax') . '<br>';
        $out .= e($this->getSynopsis('long', true)) . '<br><br>';

        if (count($this->getUsages())) {
            $out .= Format::alert('Usage') . '<br>';

            foreach ($this->getUsages() as $usage) {
                $out .= '<code>' . $usage . '</code><br><br>';
            }
        }

        if ($this->getRelated()) {
            $out .= Format::alert('See Also') . '<br>';
            foreach ($this->getRelated() as $related)
            $out .= $related . '<br><br>';
        }

        if (count($this->getAliases())) {
            $out .= Format::alert('Aliases') . '<br>';
            $out .= implode(', ', $this->aliases) . '<br><br>';
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
                $out .= $option->getDescription() . '<br><br>';
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
                            ->lists('name')
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

}
