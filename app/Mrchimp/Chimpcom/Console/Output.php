<?php

namespace Mrchimp\Chimpcom\Console;

use Illuminate\Support\Facades\Auth;
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
            'id'   => 0,
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
    public function populateUserDetails()
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
     * Append some text to the output.
     *
     * @param string $str The text to append
     * @deprecated Use write instead
     */
    public function say($str)
    {
        $this->write($str);
    }

    /**
     * Format a string and then write() it.
     *
     * @param  string $str
     */
    public function error($str)
    {
        $this->write(Format::error($str));
    }

    /**
     * Format a string and then write() it.
     *
     * @param  string $str
     */
    public function alert($str)
    {
        $this->write(Format::alert($str));
    }

    /**
     * Format a string and then write() it.
     *
     * @param  string $str
     */
    public function grey($str)
    {
        $this->write(Format::grey($str));
    }

    /**
     * Format a string and then write() it.
     *
     * @param  string $str
     */
    public function title($str)
    {
        $this->write(Format::title($str));
    }

    /**
     * Get output in JSON format
     * @return string JSON Command response
     */
    public function getJsonResponse()
    {
        return response()->json(
            $this->out,
            $this->status_code
        );
    }

    /**
     * Return the output HTML only.
     * @return string Response HTML
     */
    public function getTextOutput()
    {
        return $this->out['cmd_out'];
    }

    /**
     * Change client input to password input
     * @param  boolean $on If true, use password input. Otherwise normal text input.
     */
    public function usePasswordInput($on = true)
    {
        $this->out['show_pass'] = !!$on;
    }

    /**
     * Output a string to the client's console
     * @param  string $str
     */
    public function log($str)
    {
        $this->out['log'] .= $str;
    }

    /**
     * Set the cmd_out (the string output to the terminal).
     * You probably don't need to call this. You probably want say().
     * @param string $str The new cmd_out
     */
    public function setCmdOut($str)
    {
        $this->out['cmd_out'] = htmlspecialchars($str);
    }

    /**
     * Insert text into the command input.
     *
     * @param string $str The string to be inserted
     */
    public function cFill($str)
    {
        $this->out['cmd_fill'] .= $str;
    }

    /**
     * Redirect the browser
     */
    public function redirect($url)
    {
        $this->out['redirect'] = $url;
    }

    /**
     * Open a new browser window
     */
    public function openWindow($url, $specs = '')
    {
        $this->out['openWindow'] = $url;
        $this->out['openWindowSpecs'] = $specs;
    }

    /**
     * Set normal input and normal action.
     */
    public function resetTerminal()
    {
        $this->setAction('normal');
        $this->response->usePasswordInput(false);
    }

    /**
     * Get user details from session. This only needs to be called after user logs in/out.
     */
    public function getUserDetails()
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
     * Output errors from a Validator object
     *
     * @param  Validator $validator Validator to output
     * @param  string    $message   Message to prepend to errors
     * @return void
     */
    public function writeErrors($validator, $message = 'There was a problem:')
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

    public function editContent(string $content): void
    {
        $this->out['edit_content'] = $content;
    }
}
