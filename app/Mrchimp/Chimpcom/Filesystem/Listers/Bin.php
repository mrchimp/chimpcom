<?php

namespace App\Mrchimp\Chimpcom\Filesystem\Listers;

use App\Mrchimp\Chimpcom\Filesystem\Contracts\Lister;
use Illuminate\Support\Arr;

class Bin implements Lister
{
    public static function list(): array
    {
        $cmds = array_keys(config('chimpcom.commands', []));

        return Arr::flatten(array_map(function ($name) {
            return [
                '📄',
                'root',
                'Oct 14 12:03',
                $name,
            ];
        }, $cmds));
    }
}
