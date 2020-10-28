<?php

namespace Mrchimp\Chimpcom;

use Illuminate\Support\Facades\Session;

class Chimpcom
{
    /**
     * Chimpcom verion number
     */
    const VERSION = 'v7.0b';

    /**
     * Input string
     *
     * @var string
     */
    protected $cmd_in;

    /**
     * Name of the current command
     *
     * @var string
     */
    protected $cmd_name;

    /**
     * Array of arguments
     *
     * @var array
     */
    protected $arguments;

    /**
     * Multiline string content
     *
     * Useful for, e.g. when editing text files
     *
     * @var string
     */
    protected $content;

    /**
     * Reset the action
     */
    public function clearAction()
    {
        $this->setAction('normal');
    }

    /**
     * Returns the action to perform.
     */
    public function currentActionName(): string
    {
        return Session::get('action', 'normal');
    }

    /**
     * Sets the action
     */
    public function setAction($str = 'normal')
    {
        Session::put('action', $str);
    }

    /**
     * Return list of command names
     */
    public function getCommandList(): array
    {
        return array_keys(config('chimpcom.commands'));
    }

    /**
     * Get the version number of Chimpcom
     */
    public function getVersion(): string
    {
        return self::VERSION;
    }
}
