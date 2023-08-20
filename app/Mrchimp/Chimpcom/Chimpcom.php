<?php

namespace Mrchimp\Chimpcom;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class Chimpcom
{
    /**
     * Chimpcom verion number
     */
    const VERSION = 'v8.1';

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
     * Returns the action to perform.
     */
    public function currentActionName(): string
    {
        throw new Exception('Chimpcom::currentActionName no longer works.');
    }

    /**
     * Sets the action
     */
    public function setAction(string $action_name = 'normal', array $data = []): string
    {
        $action_id = Str::random();

        Redis::set($action_id, json_encode([
            'action_name' => $action_name,
            'data' => $data,
        ]));

        return $action_id;
    }

    public function setActionData(string $action_id, $key, $value): void
    {
        $action = Redis::get($action_id);

        if (!$action) {
            return;
        }
    }

    public function actionExists(string $action_id): bool
    {
        return Redis::exists($action_id);
    }

    public function getAction(string $action_id): ?array
    {
        $action = Redis::get($action_id);

        if (!$action) {
            return null;
        }

        return json_decode($action, true);
    }

    public function delAction(string $action_id = null): void
    {
        if ($action_id) {
            Redis::del($action_id);
        }
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
