<?php

namespace App\Mrchimp\Chimpcom;

use App\Mrchimp\Chimpcom\Actions\Action;
use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\Console\Input;
use Mrchimp\Chimpcom\Console\Output;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Log;
use Mrchimp\Chimpcom\Models\Alias;
use Mrchimp\Chimpcom\Models\Oneliner;
use Mrchimp\Chimpcom\Models\Shortcut;
use Psy\Exception\FatalErrorException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\StringInput;

class Responder
{
    protected $cmd_in;

    protected $content;

    /**
     * Log handler
     *
     * @var Log
     */
    protected $log;

    public function __construct(string $cmd_in = null, ?string $content = null)
    {
        $this->cmd_in = $cmd_in;

        if (!$this->cmd_in) {
            $this->cmd_in = '';
        }

        // Catch "@username foo" messages shorthand
        if (substr($this->cmd_in, 0, 1) === '@') {
            $this->cmd_in = 'message ' . $this->cmd_in;
        }

        $this->parts = explode(' ', trim($this->cmd_in), 2);
        $this->cmd_name = Alias::lookup($this->parts[0]);
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
        Chimpcom::clearAction();
        $output = new Output();
        $output->write(Format::alert('Aborted.'));
        return $output;
    }

    /**
     * If the current action is not 'normal
     */
    protected function isAction(): bool
    {
        $action = Chimpcom::currentActionName();

        return $action !== 'normal' && Action::exists($action);
    }

    /**
     * Whether there is a matching shortcut
     */
    protected function isShortcut(): ?Shortcut
    {
        return Shortcut::where('name', strtolower($this->cmd_name))->take(1)->first();
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
        $output = new Output();

        try {
            $command = Command::make($this->cmd_name);
        } catch (FatalErrorException $e) {
            $this->log->error('Failed to load command: ' . $this->cmd_name);
            $output->error('Failed to load command.');
            return $output;
        }

        if (!$command) {
            $this->log->error('Command with missing command class called: ' . e($this->cmd_name));
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
        $action_name = Chimpcom::currentActionName();
        $input = new Input($this->cmd_in);
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

        return $output;
    }

    public function run(): Output
    {
        if ($this->cmd_in === 'clearaction') {
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

    public function tabComplete()
    {
        $input = new StringInput($this->arguments);
        $output = new Output;

        $command = Command::make($this->cmd_name);

        return response()->json($command->tabcomplete($input, $output));
    }
}
