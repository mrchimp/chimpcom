<?php

namespace Mrchimp\Chimpcom;

use App\Mrchimp\Chimpcom\Actions\Action;
use App\Mrchimp\Chimpcom\Responder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\Console\Input;
use Mrchimp\Chimpcom\Console\Output;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Alias;
use Mrchimp\Chimpcom\Models\Message;
use Mrchimp\Chimpcom\Models\Oneliner;
use Mrchimp\Chimpcom\Models\Shortcut;
use Psy\Exception\FatalErrorException;
use Symfony\Component\Console\Exception\RuntimeException;

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

    public function clearAction()
    {
        $this->setAction('normal');
    }

    /**
     * Returns the action to perform.
     * The action bypasses the normal command processing. e.g. for passwords
     */
    public function currentActionName(): string
    {
        return Session::get('action', 'normal');
    }

    /**
     * Sets the action - i.e. what to expect from the next command.
     * If they've just entered a username, we're gonna expect a password.
     */
    public function setAction($str = 'normal')
    {
        Session::put('action', $str);
    }

    /**
     * Convert integer ID to front-facing id
     *
     * @param  integer $id Decoded id
     * @return string      Encoded id
     */
    public function encodeId(int $id): string
    {
        return dechex($id);
    }

    /**
     * Encode an array of Ids
     */
    public function encodeIds(array $ids): array
    {
        foreach ($ids as &$id) {
            $id = self::encodeId($id);
        }

        return $ids;
    }

    /**
     * Convert front-facing id to integer
     */
    public function decodeId(string $id): int
    {
        try {
            return hexdec($id);
        } catch (\Exception $e) {
            return -1;
        }
    }

    /**
     * Decode an array of IDs
     */
    public function decodeIds(array $ids): array
    {
        foreach ($ids as &$id) {
            $id = self::decodeId($id);
        }

        return $ids;
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
     *
     * @return string
     */
    public function getVersion(): string
    {
        return self::VERSION;
    }
}
