<?php

return [
    'command_log_file' => base_path() . '/storage/logs/chimpcom.log',
    'unknown_cmd_txt' => 'Unknown command. ',
    'commands' => [
        'addshortcut' => \Mrchimp\Chimpcom\Commands\Addshortcut::class,
        'alias' => \Mrchimp\Chimpcom\Commands\Alias::class,
        'aliases' => \Mrchimp\Chimpcom\Commands\Aliases::class,
        'are' => \Mrchimp\Chimpcom\Commands\Are::class,
        'base64decode' => \Mrchimp\Chimpcom\Commands\Base64decode::class,
        'base64encode' => \Mrchimp\Chimpcom\Commands\Base64encode::class,
        'candyman' => \Mrchimp\Chimpcom\Commands\Candyman::class,
        'cd' => \Mrchimp\Chimpcom\Commands\Cd::class,
        'charmap' => \Mrchimp\Chimpcom\Commands\Charmap::class,
        'chpass' => \Mrchimp\Chimpcom\Commands\Chpass::class,
        'coin' => \Mrchimp\Chimpcom\Commands\Coin::class,
        'date' => \Mrchimp\Chimpcom\Commands\Date::class,
        'deal' => \Mrchimp\Chimpcom\Commands\Deal::class,
        'dechex' => \Mrchimp\Chimpcom\Commands\Dechex::class,
        'does' => \Mrchimp\Chimpcom\Commands\Does::class,
        'done' => \Mrchimp\Chimpcom\Commands\Done::class,
        'doecho' => \Mrchimp\Chimpcom\Commands\Doecho::class,
        'find' => \Mrchimp\Chimpcom\Commands\Find::class,
        'forget' => \Mrchimp\Chimpcom\Commands\Forget::class,
        'go' => \Mrchimp\Chimpcom\Commands\Go::class,
        'hexdec' => \Mrchimp\Chimpcom\Commands\Hexdec::class,
        'hi' => \Mrchimp\Chimpcom\Commands\Hi::class,
        'lipsum' => \Mrchimp\Chimpcom\Commands\Lipsum::class,
        'login' => \Mrchimp\Chimpcom\Commands\Login::class,
        'logout' => \Mrchimp\Chimpcom\Commands\Logout::class,
        'magiceightball' => \Mrchimp\Chimpcom\Commands\Magiceightball::class,
        'mail' => \Mrchimp\Chimpcom\Commands\Mail::class,
        'man' => \Mrchimp\Chimpcom\Commands\Man::class,
        'message' => \Mrchimp\Chimpcom\Commands\Message::class,
        'monkeys' => \Mrchimp\Chimpcom\Commands\Monkeys::class,
        'newtask' => \Mrchimp\Chimpcom\Commands\Newtask::class,
        'oneliner' => \Mrchimp\Chimpcom\Commands\Oneliner::class,
        'parser' => \Mrchimp\Chimpcom\Commands\Parser::class,
        'priority' => \Mrchimp\Chimpcom\Commands\Priority::class,
        'project' => \Mrchimp\Chimpcom\Commands\Project::class,
        'projects' => \Mrchimp\Chimpcom\Commands\Projects::class,
        'register' => \Mrchimp\Chimpcom\Commands\Register::class,
        'rss' => \Mrchimp\Chimpcom\Commands\Rss::class,
        'save' => \Mrchimp\Chimpcom\Commands\Save::class,
        'scale' => \Mrchimp\Chimpcom\Commands\Scale::class,
        'setpublic' => \Mrchimp\Chimpcom\Commands\Setpublic::class,
        'show' => \Mrchimp\Chimpcom\Commands\Show::class,
        'shortcuts' => \Mrchimp\Chimpcom\Commands\Shortcuts::class,
        'stats' => \Mrchimp\Chimpcom\Commands\Stats::class,
        'styles' => \Mrchimp\Chimpcom\Commands\Styles::class,
        'tabtest' => \Mrchimp\Chimpcom\Commands\Tabtest::class,
        'tea' => \Mrchimp\Chimpcom\Commands\Tea::class,
        'tetris' => \Mrchimp\Chimpcom\Commands\Tetris::class,
        'todo' => \Mrchimp\Chimpcom\Commands\Todo::class,
        'uname' => \Mrchimp\Chimpcom\Commands\Uname::class,
        'users' => \Mrchimp\Chimpcom\Commands\Users::class,
        'version' => \Mrchimp\Chimpcom\Commands\Version::class,
        'whoami' => \Mrchimp\Chimpcom\Commands\Whoami::class,
        'who' => \Mrchimp\Chimpcom\Commands\Who::class,
    ],
    'actions' => [
        'candyman' => \Mrchimp\Chimpcom\Actions\Candyman::class,
        'done' => \Mrchimp\Chimpcom\Actions\Done::class,
        'forget' => \Mrchimp\Chimpcom\Actions\Forget::class,
        'newproject' => \Mrchimp\Chimpcom\Actions\Newproject::class,
        'password' => \Mrchimp\Chimpcom\Actions\Password::class,
        'project_rm' => \Mrchimp\Chimpcom\Actions\Project_rm::class,
        'register' => \Mrchimp\Chimpcom\Actions\Register::class,
        'register2' => \Mrchimp\Chimpcom\Actions\Register2::class,
        'register3' => \Mrchimp\Chimpcom\Actions\Register3::class,
        'chpass_1' => \Mrchimp\Chimpcom\Actions\Chpass_1::class,
        'chpass_2' => \Mrchimp\Chimpcom\Actions\Chpass_2::class,
    ]
];
