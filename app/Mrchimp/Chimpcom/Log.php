<?php

namespace Mrchimp\Chimpcom;

use Illuminate\Support\Facades\Auth;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log
{
    protected $log;

    public function __construct()
    {
        $username = Auth::check() ? Auth::user()->name : 'Guest';
        $dateFormat = "Y-m-d H:i:s";
        $output = "[%datetime%] [%level_name%] [{$username}] %message%\n";

        $formatter = new LineFormatter($output, $dateFormat);

        $stream = new StreamHandler(storage_path().'/logs/chimpcom.log');
        $stream->setFormatter($formatter);

        $this->log = new Logger(__METHOD__);
        $this->log->pushHandler($stream);
    }

    /**
     * Log info
     */
    public function info(string $msg): void
    {
        $this->log->info($msg);
    }

    /**
     * Log an error
     */
    public function error(string $msg): void
    {
        $this->log->error($msg);
    }
}
