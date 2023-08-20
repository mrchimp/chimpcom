<?php

namespace App\Mrchimp\Chimpcom;

use Auth;
use Mrchimp\Chimpcom\Log;
use Illuminate\Support\Arr;
use Mrchimp\Chimpcom\Models\Alias;
use Mrchimp\Chimpcom\Console\Input;
use Mrchimp\Chimpcom\Console\Output;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Models\Oneliner;
use Mrchimp\Chimpcom\Models\Shortcut;
use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Psy\Exception\FatalErrorException;
use Mrchimp\Chimpcom\Actions\Action;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Exception\RuntimeException;

class Responder
{
    protected $cmd_in;

    protected $cmd_name;

    protected $action_id;

    protected $action;

    protected $parts = [];

    protected $arguments = [];

    protected $content;

    /**
     * Log handler
     *
     * @var Log
     */
    protected $log;

    public function __construct(string $cmd_in = null, ?string $content = null, ?string $action_id = null)
    {
        $cmd_in = trim($cmd_in);
        $this->action_id = $action_id;

        if (!$cmd_in) {
            $cmd_in = '';
        }

        // Catch "@username foo" messages shorthand
        if (substr($cmd_in, 0, 1) === '@') {
            $cmd_in = 'message ' . $cmd_in;
        }

        $this->parts = explode(' ', $cmd_in, 2);
        $this->cmd_name = Alias::lookup($this->parts[0]);

        $this->cmd_in = implode(' ', [
            $this->cmd_name,
            ...array_slice($this->parts, 1)
        ]);
        $this->content = $content;
        $this->log = new Log;

        if (isset($this->parts[1])) {
            $this->arguments = $this->parts[1];
        } else {
            $this->arguments = '';
        }
    }

    /**
     * Clear the current action
     */
    protected function doClearAction(): Output
    {
        Chimpcom::delAction($this->action_id);
        $output = new Output();
        $output->write(Format::alert('Aborted.'));
        return $output;
    }

    /**
     * If the current action is not 'normal
     */
    protected function isAction(): bool
    {
        if (!$this->action_id) {
            return false;
        }

        if (!Chimpcom::actionExists($this->action_id)) {
            return false;
        }

        $action = Chimpcom::getAction($this->action_id);

        if (!Action::exists(Arr::get($action, 'action_name'))) {
            return false;
        }

        return true;
    }

    /**
     * Whether there is a matching shortcut
     */
    protected function isShortcut(): ?Shortcut
    {
        $shortcut = Shortcut::query()
            ->where('name', strtolower($this->cmd_name))
            ->where(function ($query) {
                $query->whereNull('user_id')->orWhere('user_id', Auth::id());
            })
            ->take(1)
            ->first();

        return $shortcut;
    }

    /**
     * Get a oneliner from an input
     */
    protected function getOneliner(): ?Oneliner
    {
        return Oneliner::query()
            ->where('command', strtolower($this->cmd_in))
            ->inRandomOrder()
            ->first();
    }

    /**
     * Handle a oneliner
     */
    protected function handleOneliner(Oneliner $oneliner): Output
    {
        $output = new Output();
        $output->write(Format::escape($oneliner->response));
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
        $output->error('Invalid command: ' . Format::escape($this->cmd_name));
        $output->setStatusCode(404);
        return $output;
    }

    /**
     * Execute a normal command
     */
    protected function handleCommand(): Output
    {
        $input = new Input($this->arguments);
        $input->setContent($this->content);
        $input->setActionId($this->action_id);
        $output = new Output();

        try {
            $command = Command::make($this->cmd_name);
        } catch (FatalErrorException $e) {
            $this->log->error('Failed to load command: ' . $this->cmd_name);
            $output->error('Failed to load command.');
            return $output;
        }

        if (!$command) {
            $this->log->error('Command with missing command class called: ' . Format::escape($this->cmd_name));
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
    protected function handleShortcut(Shortcut $shortcut): Output
    {
        $input = new Input($this->arguments);
        $input->setContent($this->content);
        $input->setActionId($this->action_id);
        $output = new Output();

        $search = $input->getFirstArgument();
        $url = str_replace('%PARAM', urlencode($search), $shortcut->url);

        $this->log->info('Shortcut: ' . $this->cmd_in);

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
        $action_name = Arr::get(Chimpcom::getAction($this->action_id), 'action_name');
        $input = new Input($this->cmd_in);
        $input->setActionId($this->action_id);
        $input->setContent($this->content);
        $output = new Output();

        try {
            $action = Command::make($action_name, 'action');
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

        $this->log->info('Action: ' . $action_name);

        return $output;
    }

    /**
     * Run the command
     */
    public function run(): Output
    {
        if ($this->cmd_in === 'clearaction' && $this->action_id) {
            return $this->doClearAction();
        }

        if ($this->isAction()) {
            return $this->handleAction();
        }

        if ($shortcut = $this->isShortcut()) {
            return $this->handleShortcut($shortcut);
        }

        if ($oneliner = $this->getOneliner()) {
            return $this->handleOneliner($oneliner);
        }

        if (Command::exists($this->cmd_name)) {
            return $this->handleCommand();
        }

        return $this->handleInvalidCommand();
    }

    /**
     * Get tab completions
     */
    public function tabComplete()
    {
        $input = new StringInput($this->arguments);
        $output = new Output;

        $command = Command::make($this->cmd_name);

        if (!$command) {
            $output = [];
        } else {
            $output = $command->tabcomplete($input, $output);
        }

        return response()->json($output);
    }
}
