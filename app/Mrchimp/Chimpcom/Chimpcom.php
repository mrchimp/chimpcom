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
    private static $available_actions = array(
        'candyman',
        'done',
        'forget',
        'newproject',
        'password',
        'project_rm',
        'register',
        'register2',
        'register3'
    );

    /**
     * Available Chimpcom commands. Used to compare input against for security.
     * @todo rather than comparing to array, should probably use something like
      *      have 'cmd' => Cmd::class
     * @var array
     */
    private static $available_commands = array(
        'addshortcut',
        'alias',
        'are',
        // 'ascii',
        'base64decode',
        'base64encode',
        'candyman',
        'cd',
        'charmap',
        // 'chpass',
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
    );

    /**
     * Get an instance of the appropriate command
     * @param  string $name
     * @return Command
     */
    static public function getCommand($name) {
        if (!in_array($name, self::$available_commands)) {
          return null;
        }

        $name = ucfirst($name);
        $command_name = "Mrchimp\Chimpcom\Commands\\".$name;
        return new $command_name;
    }

    /**
     * Take an input string and return a resopnse array
     * @param  string           $input the user input string.
     * @return ChimpcomResponse        Response object
     */
    public function respond($cmd_in) {
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
            $command = new BypassCommand();
            return $command->run('clearaction', Format::alert('Ok.'));
        }

        // Do check for non-normal action
        $action = $this->getAction();

        if ($action !== 'normal') {
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
            $response = new Response;
            $response->say($oneliner->response);
            return $response;
        }

        // Normal command?
        if (in_array($cmd_name, self::$available_commands)) {
            return $this->handleCommand($cmd_name, $arguments);
        }

        // I give up
        $command = new UnknownCommand();
        return $command->run($this->input); // @todo
    }

    /**
     * Execute a normal command
     */
    private function handleCommand($cmd_name, $cmd_in) {
        try {
            $command = $this->getCommand($cmd_name);
        } catch (FatalErrorException $e) {
            $response = new Response;
            $response->say('Failed to load command.');

            return $response;
        }

        $input = new StringInput($cmd_in);
        $output = new Output();

        try {
            $command->run($input, $output);
        } catch (RuntimeException $e) {
            $output->error('Bad input: ' . $e->getMessage());

            return $output;
        }

        return $output;
    }

    private function handleShortcut($shortcut, $cmd_in) {
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

    private function handleAction($action, $cmd_in) {
        // @todo -this!
        if (in_array($action, self::$available_actions)) {
            try {
                $command_name = "Mrchimp\Chimpcom\Actions\\".ucfirst($action);
                $command = new $command_name;
                return $command->run($this->input);
            } catch (Exception $e) {
                trigger_error('Invalid action: '.$action);
                $this->setAction();
                $response = new Response();
                $response->say(Format::error('Invalid action. This should not have happened.'));
                return $response;
            }
        } else {
            $response = new Response();
            $response->error('Invalid action: '.htmlspecialchars($action));
            return $response;
        }
    }


    /**
     * Returns the action to perform.
     * The action bypasses the normal command processing. e.g. for passwords
     *
     * @return string the name of the action to take. Default: 'normal'.
     */
    private function getAction() {
        return Session::get('action', 'normal');
    }

    /**
     * Sets the action - i.e. what to expect from the next command.
     * If they've just entered a username, we're gonna expect a password.
     *
     * @param string $str the name of the action to expect
     */
    protected function setAction($str = 'normal') {
        Session::set('action', $str);
    }

    /**
     * Convert integer ID to front-facing id
     * @param  integer $id Decoded id
     * @return string      Encoded id
     */
    static function encodeId($id) {
        return dechex($id);
    }

    /**
     * Convert front-facing id to integer
     * @param  string $id Encoded id
     * @return integer    Decoded id
     */
    static function decodeId($id) {
        return hexdec($id);
    }

    /**
     * Render a welcome message
     * @return string
     */
    static function welcomeMessage() {
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
    static public function getCommandList() {
        return self::$available_commands;
    }

}
