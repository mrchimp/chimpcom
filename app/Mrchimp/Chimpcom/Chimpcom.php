<?php

namespace Mrchimp\Chimpcom;

use App\Mrchimp\Chimpcom\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\Console\Output;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Log;
use Mrchimp\Chimpcom\Models\Alias;
use Mrchimp\Chimpcom\Models\Message;
use Mrchimp\Chimpcom\Models\Oneliner;
use Mrchimp\Chimpcom\Models\Shortcut;
use Psy\Exception\FatalErrorException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\StringInput;

class Chimpcom
{
    /**
     * Chimpcom verion number
     */
    const VERSION = 'v7.0b';

    /**
     * Log handler
     *
     * @var Log
     */
    protected $log;

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

    public function __construct()
    {
        $this->log = new Log;
    }

    /**
     * Get an instance of the appropriate command
     */
    public static function instantiateCommand(string $name): ?Command
    {
        if (static::commandExists($name)) {
            $command_class = config('chimpcom.commands.' . strtolower($name));

            return new $command_class;
        }

        return null;
    }

    /**
     * Check if a command exists by name
     */
    public static function commandExists(string $name): Bool
    {
        return !!config('chimpcom.commands.' . strtolower($name));
    }

    /**
     * Get an instance of the appropriate action
     */
    public static function instantiateAction(string $name): ?Action
    {
        if (static::actionExists($name)) {
            $action_class = config('chimpcom.actions.' . strtolower($name));

            return new $action_class;
        }

        return null;
    }

    /**
     * Check if an action exists by name
     */
    public static function actionExists(string $name): Bool
    {
        return !!config('chimpcom.actions.' . strtolower($name));
    }

    /**
     * Take an input string and return a response array
     */
    public function respond(string $cmd_in): Output
    {
        $this->cmd_in = $cmd_in;

        // Catch "@username foo" messages shorthand
        if (substr($this->cmd_in, 0, 1) === '@') {
            $this->cmd_in = 'message ' . $this->cmd_in;
        }

        $parts = explode(' ', trim($this->cmd_in), 2);

        $this->cmd_name = Alias::lookup($parts[0]);

        if (isset($parts[1])) {
            $this->arguments = $parts[1];
        } else {
            $this->arguments = '';
        }

        if ($this->cmd_in === 'clearaction') {
            return $this->doClearAction();
        }

        if ($this->isSpecialAction()) {
            return $this->handleAction();
        }

        if ($shortcut = $this->isShortcut()) {
            return $this->handleShortcut($shortcut, $this->cmd_name, $this->arguments);
        }

        if ($oneliner = $this->getOneliner()) {
            return $this->handleOneliner($oneliner);
        }

        if ($this->commandExists($this->cmd_name)) {
            return $this->handleCommand($this->cmd_name, $this->arguments);
        }

        return $this->handleInvalidCommand();
    }

    /**
     * Clear the current action
     */
    protected function doClearAction(): Output
    {
        $this->setAction('normal');
        $output = new Output();
        $output->write(Format::alert('Ok.'));
        return $output;
    }

    /**
     * If the current action is not 'normal
     */
    protected function isSpecialAction(): bool
    {
        $action = $this->getAction();

        return $action !== 'normal' && $this->actionExists($action);
    }

    /**
     * Whether there is a matching shortcut
     */
    protected function isShortcut(): ?Shortcut
    {
        return Shortcut::where('name', $this->cmd_name)->take(1)->first();
    }

    /**
     * Get a oneliner from an input
     */
    protected function getOneliner(): ?Oneliner
    {
        return Oneliner::query()
            ->where('command', $this->cmd_name)
            ->inRandomOrder()
            ->first();
    }

    /**
     * Handle a oneliner
     */
    protected function handleOneliner(Oneliner $oneliner): Output
    {
        $output = new Output();
        $output->write($oneliner->response);
        $this->log->info('Oneliner: ' . $this->cmd_name);
        return $output;
    }

    /**
     * Handle an unhandleable request
     */
    protected function handleInvalidCommand(): Output
    {
        $output = new Output();
        $this->log->error('Invalid command: ' . $this->cmd_in);
        $output->error('Invalid command: ' . htmlspecialchars($this->cmd_name));
        $output->setResponseCode(404);
        return $output;
    }

    /**
     * Execute a normal command
     */
    protected function handleCommand(string $cmd_name, string $cmd_in): Output
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
    protected function handleShortcut(Shortcut $shortcut, string $cmd_in, string $args): Output
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
    protected function handleAction(): Output
    {
        $action_name = $this->getAction();
        $input = new StringInput($this->cmd_in);
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
     * Return list of command names
     */
    public static function getCommandList(): array
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
