<?php

namespace Mrchimp\Chimpcom\Console;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Format;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\Output as SymfonyOutput;

class Output extends SymfonyOutput
{
    protected $status_code = 200;

    /**
     * Response array. This is what will eventually be returned.
     *
     * @var array
     */
    private $out = [
        'cmd_out'   => '',
        'show_pass' => false,
        'cmd_fill'  => null,
        'log'       => null,
        'user'      => [
            'id'   => -1,
            'name' => 'Guest',
        ],
        'edit_content' => null,
    ];

    public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = false, OutputFormatterInterface $formatter = null)
    {
        $this->populateUserDetails();

        parent::__construct($verbosity, $decorated, $formatter);
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite($message, $newline)
    {
        $this->out['cmd_out'] .= $message;

        if ($newline) {
            $this->out['cmd_out'] .= '<br>';
        }
    }

    /**
     * Get user details from session.
     */
    public function populateUserDetails(): void
    {
        if (Auth::check()) {
            $this->user = Auth::user();
            $this->out['user']['id'] = $this->user->id;
            $this->out['user']['name'] = $this->user->name;
        } else {
            $this->out['user']['id'] = -1;
            $this->out['user']['name'] = 'Guest';
        }
    }

    /**
     * @deprecated Use populateUserDetails instead
     */
    public function getUserDetails(): void
    {
        $this->populateUserDetails();
    }

    /**
     * Append some text to the output.
     *
     * @deprecated Use write instead
     */
    public function say(string $str, bool $newline = false): void
    {
        $this->write($str, $newline);
    }

    /**
     * Format a string and then write() it.
     */
    public function error(string $str, bool $newline = false): void
    {
        $this->write(Format::error($str), $newline);
    }

    /**
     * Format a string and then write() it.
     */
    public function alert(string $str, bool $newline = false): void
    {
        $this->write(Format::alert($str), $newline);
    }

    /**
     * Format a string and then write() it.
     */
    public function grey(string $str, bool $newline = false): void
    {
        $this->write(Format::grey($str), $newline);
    }

    /**
     * Format a string and then write() it.
     */
    public function title(string $str, bool $newline = false): void
    {
        $this->write(Format::title($str), $newline);
    }

    /**
     * Get output in JSON format
     */
    public function getJsonResponse(): JsonResponse
    {
        return response()->json(
            $this->out,
            $this->status_code
        );
    }

    /**
     * Return the output HTML only.
     */
    public function getTextOutput(): string
    {
        return $this->out['cmd_out'];
    }

    /**
     * Change client input to password input
     *
     * If $on is true, use password input. Otherwise normal text input.
     */
    public function usePasswordInput(bool $on = true): void
    {
        $this->out['show_pass'] = !!$on;
    }

    /**
     * Output a string to the client's console
     */
    public function log(string $str): void
    {
        $this->out['log'] .= $str;
    }

    /**
     * Set the cmd_out (the string output to the terminal).
     *
     * You probably don't need to call this. You probably want write().
     */
    public function setCmdOut(string $cmd_out): void
    {
        $this->out['cmd_out'] = htmlspecialchars($cmd_out);
    }

    /**
     * Insert text into the command input.
     */
    public function cFill(string $command): void
    {
        $this->out['cmd_fill'] .= $command;
    }

    /**
     * Redirect the browser
     */
    public function redirect(string $url): void
    {
        $this->out['redirect'] = $url;
    }

    /**
     * Open a new browser window
     */
    public function openWindow(string $url, string $specs = ''): void
    {
        $this->out['openWindow'] = $url;
        $this->out['openWindowSpecs'] = $specs;
    }

    /**
     * Set normal input and normal action.
     */
    public function resetTerminal(): void
    {
        Chimpcom::setAction('normal');
        $this->usePasswordInput(false);
    }

    /**
     * Output errors from a Validator object
     */
    public function writeErrors(Validator $validator, string $message = 'There was a problem:'): void
    {
        $errors = $validator->errors();

        $this->error($message . '<br>');

        foreach ($errors->all() as $message) {
            $this->error(' &bullet; ' . $message . '<br>');
        }
    }

    /**
     * Set HTTP Status Code
     */
    public function setStatusCode(int $code): void
    {
        $this->status_code = $code;
    }

    /**
     * Get HTTP Status Code
     */
    public function getStatusCode(): int
    {
        return $this->status_code;
    }

    /**
     * Provide content to be edited
     */
    public function editContent(string $content): void
    {
        $this->out['edit_content'] = $content;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return $this->out;
    }
}
