<?php

return [
    'command_log_file' => base_path() . '/storage/logs/chimpcom.log',
    'unknown_cmd_txt' => 'Unknown command. ',
    'commands' => [
        'alias' => \Mrchimp\Chimpcom\Commands\Alias::class,
        'are' => \Mrchimp\Chimpcom\Commands\Are::class,
        'base64decode' => \Mrchimp\Chimpcom\Commands\Base64decode::class,
        'base64encode' => \Mrchimp\Chimpcom\Commands\Base64encode::class,
        'candyman' => \Mrchimp\Chimpcom\Commands\Candyman::class,
        'cat' => \Mrchimp\Chimpcom\Commands\Cat::class,
        'cd' => \Mrchimp\Chimpcom\Commands\Cd::class,
        'charmap' => \Mrchimp\Chimpcom\Commands\Charmap::class,
        'chpass' => \Mrchimp\Chimpcom\Commands\Chpass::class,
        'coin' => \Mrchimp\Chimpcom\Commands\Coin::class,
        'date' => \Mrchimp\Chimpcom\Commands\Date::class,
        'deal' => \Mrchimp\Chimpcom\Commands\Deal::class,
        'dechex' => \Mrchimp\Chimpcom\Commands\Dechex::class,
        'diary' => \Mrchimp\Chimpcom\Commands\Diary::class,
        'does' => \Mrchimp\Chimpcom\Commands\Does::class,
        'doecho' => \Mrchimp\Chimpcom\Commands\Doecho::class,
        'edit' => \Mrchimp\Chimpcom\Commands\Edit::class,
        'go' => \Mrchimp\Chimpcom\Commands\Go::class,
        'hexdec' => \Mrchimp\Chimpcom\Commands\Hexdec::class,
        'hi' => \Mrchimp\Chimpcom\Commands\Hi::class,
        'lipsum' => \Mrchimp\Chimpcom\Commands\Lipsum::class,
        'login' => \Mrchimp\Chimpcom\Commands\Login::class,
        'logout' => \Mrchimp\Chimpcom\Commands\Logout::class,
        'ls' => \App\Mrchimp\Chimpcom\Commands\Ls::class,
        'magiceightball' => \Mrchimp\Chimpcom\Commands\Magiceightball::class,
        'mail' => \Mrchimp\Chimpcom\Commands\Mail::class,
        'man' => \Mrchimp\Chimpcom\Commands\Man::class,
        'message' => \Mrchimp\Chimpcom\Commands\Message::class,
        'mkdir' => \Mrchimp\Chimpcom\Commands\Mkdir::class,
        'mkfile' => \Mrchimp\Chimpcom\Commands\Mkfile::class,
        'monkeys' => \Mrchimp\Chimpcom\Commands\Monkeys::class,
        'note:find' => \Mrchimp\Chimpcom\Commands\NoteFind::class,
        'note:forget' => \Mrchimp\Chimpcom\Commands\NoteForget::class,
        'note:new' => \Mrchimp\Chimpcom\Commands\NoteNew::class,
        'note:public' => \Mrchimp\Chimpcom\Commands\NotePublic::class,
        'note:show' => \Mrchimp\Chimpcom\Commands\NoteShow::class,
        'oneliner' => \Mrchimp\Chimpcom\Commands\Oneliner::class,
        'parser' => \Mrchimp\Chimpcom\Commands\Parser::class,
        'priority' => \Mrchimp\Chimpcom\Commands\Priority::class,
        'project' => \Mrchimp\Chimpcom\Commands\Project::class,
        'pwd' => \Mrchimp\Chimpcom\Commands\Pwd::class,
        'register' => \Mrchimp\Chimpcom\Commands\Register::class,
        'rm' => \Mrchimp\Chimpcom\Commands\Rm::class,
        'rmdir' => \Mrchimp\Chimpcom\Commands\Rmdir::class,
        'rss' => \Mrchimp\Chimpcom\Commands\Rss::class,
        'scale' => \Mrchimp\Chimpcom\Commands\Scale::class,
        'shortcut' => \Mrchimp\Chimpcom\Commands\Shortcut::class,
        'shortcuts' => \Mrchimp\Chimpcom\Commands\Shortcuts::class,
        'stats' => \Mrchimp\Chimpcom\Commands\Stats::class,
        'styles' => \Mrchimp\Chimpcom\Commands\Styles::class,
        'sudo' => \Mrchimp\Chimpcom\Commands\Sudo::class,
        'tabtest' => \Mrchimp\Chimpcom\Commands\Tabtest::class,
        'tag' => \Mrchimp\Chimpcom\Commands\Tag::class,
        'task' => \Mrchimp\Chimpcom\Commands\Task::class,
        'task:done' => \Mrchimp\Chimpcom\Commands\TaskDone::class,
        'task:edit' => \Mrchimp\Chimpcom\Commands\TaskEdit::class,
        'task:new' => \Mrchimp\Chimpcom\Commands\TaskNew::class,
        'task:tag' => \Mrchimp\Chimpcom\Commands\TaskTag::class,
        'tea' => \Mrchimp\Chimpcom\Commands\Tea::class,
        'tetris' => \Mrchimp\Chimpcom\Commands\Tetris::class,
        'token' => \Mrchimp\Chimpcom\Commands\Token::class,
        'uname' => \Mrchimp\Chimpcom\Commands\Uname::class,
        'users' => \Mrchimp\Chimpcom\Commands\Users::class,
        'version' => \Mrchimp\Chimpcom\Commands\Version::class,
        'whoami' => \Mrchimp\Chimpcom\Commands\Whoami::class,
        'who' => \Mrchimp\Chimpcom\Commands\Who::class,
    ],
    'actions' => [
        'candyman' => \Mrchimp\Chimpcom\Actions\Candyman::class,
        'done' => \Mrchimp\Chimpcom\Actions\Done::class,
        'edit' => \Mrchimp\Chimpcom\Actions\Edit::class,
        'forget' => \Mrchimp\Chimpcom\Actions\Forget::class,
        'newproject' => \Mrchimp\Chimpcom\Actions\Newproject::class,
        'password' => \Mrchimp\Chimpcom\Actions\Password::class,
        'project_rm' => \Mrchimp\Chimpcom\Actions\Project_rm::class,
        'register' => \Mrchimp\Chimpcom\Actions\Register::class,
        'register2' => \Mrchimp\Chimpcom\Actions\Register2::class,
        'register3' => \Mrchimp\Chimpcom\Actions\Register3::class,
        'chpass_1' => \Mrchimp\Chimpcom\Actions\Chpass_1::class,
        'chpass_2' => \Mrchimp\Chimpcom\Actions\Chpass_2::class,
        'edit_task' => \Mrchimp\Chimpcom\Actions\EditTask::class,
    ]
];
