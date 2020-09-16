<?php

/**
 * Main Chimpcom object.
 */

namespace Mrchimp\Chimpcom;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\Console\Output;
use Mrchimp\Chimpcom\Log;
use Mrchimp\Chimpcom\Models\Alias as ChimpcomAlias;
use Mrchimp\Chimpcom\Models\Message;
use Mrchimp\Chimpcom\Models\Oneliner;
use Mrchimp\Chimpcom\Models\Shortcut;
use Psy\Exception\FatalErrorException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\StringInput;

/**
 * Main Chimpcom object.
 * Any wrappers or ports should be based around this class.
 */
class Chimpcom
{
    /**
     * Chimpcom verion number
     */
    const VERSION = 'v7.0b';

    /**
     * Available actions. Actions are alternative command modes used to accept
     * special input. Normal operation is to have action set to 'normal'.
     *
     * @var array
     */
    private static $available_actions = [
        'candyman',
        'done',
        'forget',
        'newproject',
        'password',
        'project_rm',
        'register',
        'register2',
        'register3',
        'chpass_1',
        'chpass_2',
    ];

    /**
     * Available Chimpcom commands. Used to compare input against for security.
     *
     * @todo rather than comparing to array, should probably use something like
     *      have 'cmd' => Cmd::class
     * @var  array
     */
    private static $available_commands = [
        'addshortcut',
        'alias',
        'aliases',
        'are',
        'base64decode',
        'base64encode',
        'candyman',
        'cd',
        'charmap',
        'chpass',
        'coin',
        'date',
        'deal',
        'dechex',
        'does',
        'done',
        'doecho',
        'find',
        'forget',
        'go',
        'hexdec',
        'hi',
        'lipsum',
        'login',
        'logout',
        'magiceightball',
        'mail',
        'man',
        'message',
        'monkeys',
        'newtask',
        'oneliner',
        'parser',
        'priority',
        'project',
        'projects',
        'register',
        'rss',
        'save',
        'scale',
        'setpublic',
        'show',
        'shortcuts',
        'stats',
        'styles',
        'tabtest',
        'tea',
        'tetris',
        'todo',
        'uname',
        'users',
        'version',
        'whoami',
        'who'
    ];

    protected $log;

    public function __construct()
    {
        $this->log = new Log;
    }

    /**
     * Get an instance of the appropriate command
     */
    public static function instantiateCommand(string $name): ?Command
    {
        if (!in_array($name, self::$available_commands)) {
            return null;
        }

        $name = ucfirst($name);
        $command_name = "Mrchimp\Chimpcom\Commands\\" . $name;

        return new $command_name;
    }

    /**
     * Get an instance of the appropriate action
     *
     * @param string $name
     * @return Command
     */
    public static function instantiateAction(string $name): ?Command
    {
        if (!in_array($name, self::$available_actions)) {
            return null;
        }

        $name = ucfirst($name);
        $action_name = "Mrchimp\Chimpcom\Actions\\" . $name;
        return new $action_name;
    }

    /**
     * Take an input string and return a response array
     */
    public function respond(string $cmd_in): Output
    {
        // Catch "@username foo" messages shorthand
        if (substr($cmd_in, 0, 1) === '@') {
            $cmd_in = "message $cmd_in";
        }

        $parts = explode(' ', trim($cmd_in), 2);

        $cmd_name = ChimpcomAlias::lookup($parts[0]);

        if (isset($parts[1])) {
            $arguments = $parts[1];
        } else {
            $arguments = '';
        }

        $this->cmd_in = $cmd_in;

        if ($cmd_in === 'clearaction') {
            $this->setAction('normal');
            $output = new Output();
            $output->write(Format::alert('Ok.'));
            return $output;
        }

        // Do check for non-normal action
        $action = $this->getAction();

        if ($action !== 'normal' && in_array($action, self::$available_actions)) {
            return $this->handleAction($action, $cmd_in);
        }

        // Check for shortcuts?
        $shortcut = Shortcut::where('name', $cmd_name)->take(1)->first();

        if (!empty($shortcut)) {
            return $this->handleShortcut($shortcut, $cmd_name, $arguments);
        }

        // Do we have a witty oneliner?
        $oneliner = Oneliner::where('command', $cmd_name)
            ->inRandomOrder()
            ->first();

        if (!empty($oneliner)) {
            $output = new Output();
            $output->write($oneliner->response);
            $this->log->info('Oneliner: ' . $cmd_name);
            return $output;
        }

        // Normal command?
        if (in_array($cmd_name, self::$available_commands)) {
            return $this->handleCommand($cmd_name, $arguments);
        }

        // I give up
        $output = new Output();
        $this->log->error('Invalid command: ' . $cmd_in);
        $output->error('Invalid command: ' . htmlspecialchars($cmd_name));
        $output->setResponseCode(404);
        return $output;
    }

    /**
     * Execute a normal command
     */
    private function handleCommand(string $cmd_name, string $cmd_in): Output
    {
        $input = new StringInput($cmd_in);
        $output = new Output();

        try {
            $command = $this->instantiateCommand($cmd_name);
        } catch (FatalErrorException $e) {
            $this->log->error('Failed to load command: ' . $cmd_name);
            $output->error('Failed to load command.');
            return $output;
        }

        try {
            $command->run($input, $output);
        } catch (RuntimeException $e) {
            $this->log->error('Bad input ' . $e->getMessage());
            $output->error('Bad input: ' . $e->getMessage());
        }

        return $output;
    }

    /**
     * Respond to a given shortcut command
     */
    private function handleShortcut(Shortcut $shortcut, string $cmd_in, string $args): Output
    {
        $input = new StringInput($args);
        $output = new Output();

        $search = $input->getFirstArgument();
        $url = str_replace('%PARAM', urlencode($search), $shortcut->url);

        $this->log->info('Shortcut: ' . $cmd_in);

        if ($input->hasParameterOption('--blank') || $input->hasParameterOption('-b')) {
            $output->openWindow($url);
        } else {
            $output->redirect($url);
        }

        $output->alert('Redirecting...');

        return $output;
    }

    /**
     * Execute an action
     */
    private function handleAction(string $action_name, string $cmd_in): Output
    {
        $input = new StringInput($cmd_in);
        $output = new Output();

        try {
            $action = $this->instantiateAction($action_name);
        } catch (FatalErrorException $e) {
            $this->log->error('Failed to load action: ' . $action_name);
            $output->error('Failed to load action.');
            return $output;
        }

        try {
            $action->run($input, $output);
        } catch (RuntimeException $e) {
            $this->log->error('Bad action input. Action: ' . $action_name);
            $output->error('Bad input: ' . $e->getMessage());
        }

        return $output;
    }

    /**
     * Returns the action to perform.
     * The action bypasses the normal command processing. e.g. for passwords
     */
    public function getAction(): string
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
    public static function encodeId(int $id): string
    {
        return dechex($id);
    }

    /**
     * Encode an array of Ids
     */
    public static function encodeIds(array $ids): array
    {
        foreach ($ids as &$id) {
            $id = self::encodeId($id);
        }

        return $ids;
    }

    /**
     * Convert front-facing id to integer
     */
    public static function decodeId(string $id): int
    {
        return hexdec($id);
    }

    /**
     * Decode an array of IDs
     */
    public static function decodeIds(array $ids): array
    {
        foreach ($ids as &$id) {
            $id = self::decodeId($id);
        }

        return $ids;
    }

    /**
     * Render a welcome message
     */
    public static function welcomeMessage(): string
    {
        $output = '';

        if (Auth::check()) {
            $user = Auth::user();
            $output .= Format::title('Welcome back, ' . e($user->name) . '.');

            $messages = Message::where('recipient_id', $user->id)
                ->where('has_been_read', false)
                ->get();

            if (count($messages) > 0) {
                $output .= '<br>You have ' . count($messages) . ' new message' .
                    (count($messages) > 1 ? 's' : '') .
                    '. Type <code>mail</code> to read. ';

                if (count($messages) > 10) {
                    $output .= 'Aren\'t you popular! ';
                }
            }
        } else {
            $output .= Format::title('Chimpcom ' . self::VERSION) . '<br>';
            $output .= 'Go ahead';
        }

        return $output;
    }

    /**
     * Return the available_commands array
     */
    public static function getCommandList(): array
    {
        return self::$available_commands;
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
