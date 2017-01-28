<?php
/**
 * Main Chimpcom object.
 */

namespace Mrchimp\Chimpcom;

use DB;
use Auth;
use Session;
use RuntimeException;
use Illuminate\Http\Request;
use Mrchimp\Chimpcom\Models\Shortcut;
use Mrchimp\Chimpcom\Models\Message;
use Mrchimp\Chimpcom\Models\Oneliner;
use Mrchimp\Chimpcom\Commands\UnknownCommand;
use Mrchimp\Chimpcom\Models\Alias as ChimpcomAlias;
use Mrchimp\Chimpcom\Console\Command;
use Mrchimp\Chimpcom\Console\Output;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\InputArgument;

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
     * Command input
     * @var ChimpcomInput
     */
    private $input;

    /**
     * Available actions. Actions are alternative command modes used to accept
     * special input. Normal operation is to have action set to 'normal'.
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
        'register3'
    ];

    /**
     * Available Chimpcom commands. Used to compare input against for security.
     * @todo rather than comparing to array, should probably use something like
      *      have 'cmd' => Cmd::class
     * @var array
     */
    private static $available_commands = [
        'addshortcut',
        'alias',
        'aliases',
        'are',
        // 'ascii',
        'base64decode',
        'base64encode',
        'candyman',
        'cd',
        'charmap',
        'chpass',
        'coin',
        'credits',
        'date',
        'deal',
        'dechex',
        'does',
        'done',
        'doecho',
        // 'email',
        // 'episode',
        'feeds',
        // 'files',
        'find',
        'forget',
        'go',
        // 'hash',
        // 'help',
        'hexdec',
        'hi',
        'lipsum',
        'login',
        'logout',
        // 'look',
        'magiceightball',
        'mail',
        'man',
        'message',
        'monkeys',
        'newtask',
        'oneliner',
        // 'phpinfo',
        'parser',
        'priority',
        'project',
        'projects',
        'reddit',
        'register',
        'save',
        'scale',
        'setpublic',
        'show',
        'shortcuts',
        'stats',
        'styles',
        'tea',
        'tetris',
        // 'thing',
        // 'this',
        'todo',
        'uname',
        'updateman',
        'users',
        'version',
        // 'weather',
        'whoami',
        'who'
    ];

    /**
     * Get an instance of the appropriate command
     *
     * @param  string $name
     * @return Command
     */
    static public function instantiateCommand($name)
    {
        if (!in_array($name, self::$available_commands)) {
            return null;
        }

        $name = ucfirst($name);
        $command_name = "Mrchimp\Chimpcom\Commands\\".$name;
        return new $command_name;
    }

    /**
     * Get an instance of the appropriate action
     *
     * @param  string $name
     * @return Command
     */
    static public function instantiateAction($name)
    {
        if (!in_array($name, self::$available_actions)) {
            return null;
        }

        $name = ucfirst($name);
        $action_name = "Mrchimp\Chimpcom\Actions\\".$name;
        return new $action_name;
    }

    /**
     * Take an input string and return a response array
     * @param  string           $input the user input string.
     * @return ChimpcomResponse        Response object
     */
    public function respond($cmd_in)
    {
        // Catch "@username foo" messages shorthand
        if (substr($cmd_in, 0, 1) === '@') {
            $cmd_in = "message $cmd_in";
        }

        $parts = explode(' ', trim($cmd_in), 2); // @todo - efficiency!

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

        if (count($shortcut) > 0) {
            return $this->handleShortcut($shortcut, $cmd_in);
        }

        // Do we have a witty oneliner?
        $oneliner = Oneliner::where('command', $cmd_name)
                                    ->orderBy(DB::raw('RAND()'))
                                    ->first();

        if (count($oneliner) > 0) {
            $output = new Output();
            $output->write($oneliner->response);
            return $output;
        }

        // Normal command?
        if (in_array($cmd_name, self::$available_commands)) {
            return $this->handleCommand($cmd_name, $arguments);
        }

        // I give up
        $output = new Output();
        $output->error('Invalid command: '.htmlspecialchars($cmd_name));
        return $output;
    }

    /**
     * Execute a normal command
     *
     * @param  string                          $cmd_name name of command
     * @param  string                          $cmd_in   whole input string
     * @return Mrchimp\Chimpcom\Console\Output
     */
    private function handleCommand($cmd_name, $cmd_in)
    {
        $input = new StringInput($cmd_in);
        $output = new Output();

        try {
            $command = $this->instantiateCommand($cmd_name);
        } catch (FatalErrorException $e) {
            $output->error('Failed to load command.');
            return $output;
        }

        try {
            $command->run($input, $output);
        } catch (RuntimeException $e) {
            $output->error('Bad input: ' . $e->getMessage());
        }

        return $output;
    }

    /**
     * Respond to a given shortcut command
     *
     * @param  string $shortcut name of the shortcut
     * @param  string $cmd_in   full command string
     * @return Output           [description]
     */
    private function handleShortcut($shortcut, $cmd_in)
    {
        // @todo - this!
        $url = str_replace('%PARAM', urlencode($this->input->get(1)), $shortcut->url);

        $response = new Response();

        if ($this->input->isFlagSet(array('--blank', '-b'))) {
            $response->openWindow($url);
        } else {
            $response->redirect($url);
        }

        $response->alert('Redirecting...');

        return $response;
    }

    /**
     * Execute an action
     *
     * @param  string                          $action_name name of action
     * @param  string                          $cmd_in   whole input string
     * @return Mrchimp\Chimpcom\Console\Output
     */
    private function handleAction($action_name, $cmd_in)
    {
        $input = new StringInput($cmd_in);
        $output = new Output();

        try {
            $action = $this->instantiateAction($action_name);
        } catch (FaralErrorException $e) {
            $output->error('Failed to load action.');
            return $output;
        }

        try {
            $action->run($input, $output);
        } catch (RuntimeException $e) {
            $output->error('Bad input: ' . $e->getMessage());
        }

        return $output;
    }

    /**
     * Returns the action to perform.
     * The action bypasses the normal command processing. e.g. for passwords
     *
     * @return string the name of the action to take. Default: 'normal'.
     */
    public function getAction()
    {
        return Session::get('action', 'normal');
    }

    /**
     * Sets the action - i.e. what to expect from the next command.
     * If they've just entered a username, we're gonna expect a password.
     *
     * @param string $str the name of the action to expect
     */
    public function setAction($str = 'normal')
    {
        Session::put('action', $str);
    }

    /**
     * Convert integer ID to front-facing id
     * @param  integer $id Decoded id
     * @return string      Encoded id
     */
    static function encodeId($id)
    {
        return dechex($id);
    }

    /**
     * Encode an array of Ids
     * @param  array $ids [description]
     * @return [type]      [description]
     */
    static function encodeIds(array $ids)
    {
        foreach ($ids as &$id) {
            $id = self::encodeId($id);
        }

        return $ids;
    }

    /**
     * Convert front-facing id to integer
     * @param  string $id Encoded id
     * @return integer    Decoded id
     */
    static function decodeId($id)
    {
        return hexdec($id);
    }

    /**
     * Decode an array of IDs
     *
     * @param  array $id
     * @return array
     */
    static function decodeIds(array $ids)
    {
        foreach ($ids as &$id) {
            $id = self::decodeId($id);
        }

        return $ids;
    }

    /**
     * Render a welcome message
     * @return string
     */
    static function welcomeMessage()
    {
        $output = '';

        if (Auth::check()) {
            $user = Auth::user();
            $output .= Format::title('Welcome back, '.e($user->name).'.');

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
            $output .= 'Hello, stranger! Don\'t be afraid. It\'s just text.';
        }

        $output .= '<br>For help type \'<code>?</code>\'';
        return $output;
    }

    /**
     * Return the available_commands array
     * @return array All command names
     */
    static public function getCommandList()
    {
        return self::$available_commands;
    }

    /**
     * Get the version number of Chimpcom
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }
}
