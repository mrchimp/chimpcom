<?php
/**
 * Abstract Chimpcom command
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Input;
use Mrchimp\Chimpcom\Response;
use Mrchimp\Chimpcom\Format;
use App;
use Auth;
use Session;
use Validator;

/**
 * Basis for all Chimpcom commands
 */
abstract class AbstractCommand
{

  /**
   * Chimpcom response object
   * @var ChimpcomResponse
   */
  protected $response;

  /**
   * Set to true if command is unknown. Used for logging purposes.
   * @var boolean
   */
  protected $unknown = false;

  /**
   * Set to true if we want to log the command. Used to hide sensitive 
   * information from log files.
   * @var boolean
   */
  protected $log_this = true;

  /**
   * File to log command history to. Configured in /config/logs/chimpcom.php
   * @var string
   */
  private $log_file;
 
  /**
   * Command input
   * @var Input
   */
  protected $input;

  /**
   * Class constructor
   */
  function __construct() {
    // app()->configure('chimpcom');
    $this->response = new Response();
    $this->log_file = config('chimpcom.command_log_file');
  }

  /**
   * Sets the current command as 'Unknown'.
   * For logging purposes only.
   */
  protected function unknownCmd() {
    $this->unknown = true;
  }

  /**
   * Log command and some meta info
   */
  protected function logCmd() {
    if ($this->log_this) {
      // $now = microtime(true);
      // $time_taken = number_format(($now - $this->start_time), 8);

      if (Auth::check()) {
        $user = Auth::user();
        $username = $user->name;
      } else {
        $username = 'Guest';
      }

      $logstr = ($this->unknown == true ? '-- ' : '   ');
      $logstr .= ' [' . $username .']';
      $logstr .= ' [' . date('Y-m-d H:i:s') . ']';
      $logstr .= $this->input->getCommand();
      // $logstr .= ' [' . $time_taken . 's]';
      $logstr .= "\r\n";
      $this->log($logstr);
    }
  }

  /**
   * Run the command
   * @param  Mrchimp\Chimpcom\Input    $input the command input
   * @return Mrchimp\Chimpcom\Response        the command response
   */
  public function run(Input $input) {
    $this->response->setCmdIn($input->get());
    $this->input = $input;
    $this->process();
    return $this->response;
  }
 
  /**
   * Saves a string to the log file
   * @param  string $msg The message to log
   */
  private function log($msg) {
    if (!$this->log_file) {
      trigger_error('Chimpcom log file not specified.');
      return false;
    }
    $fh = fopen($this->log_file, 'a');
    fwrite($fh, $msg);
    fclose($fh);
  }

  /**
   * Returns the action to perform.
   * The action bypasses the normal command processing. e.g. for passwords
   *
   * @return string the name of the action to take. Default: 'normal'.
   */
  protected function getAction() {
    return Session::get('action', 'normal');//(empty($_SESSION['action']) ? 'normal' : $_SESSION['action']);
  }

  /**
   * Sets the action - i.e. what to expect from the next command.
   * If they've just entered a username, we're gonna expect a password.
   * 
   * @param string $str the name of the action to expect
   */
  protected function setAction($str = 'normal'){
    Session::set('action', $str);
  }

  /**
   * Do the meat of the process.
   * @param  string $command Command input.
   * @param  string $output  Command output (for forcing the output)
   * @return ChimpcomResponse           
   */
  abstract public function process();

  /**
   * Validate some input. If it fails, reset everything.
   * @action normal
   * @param  array $data   The data to validate
   * @param  array $errors The rules to validate against
   */
  public function validateOrDie($data, $rules) {
    $validator = Validator::make($data, $rules);

    if ($validator->fails()) {
      $messages = $validator->errors();

      foreach ($messages->all() as $error) {
        $this->response->error($error);
      }

      $this->resetTerminal();

      return false;
    } else {
      return true;
    }
  }

  /**
   * Set normal input and normal action
   */
  public function resetTerminal() {
    $this->setAction('normal');
    $this->response->usePasswordInput(false);
  }

  public  function runTabcomplete(Input $input) {
    $this->input = $input;
    return $this->tabcomplete();
  }

  /**
   * Return first guess at parameter completion
   * @return String The full completed command input.
   *                Empty string if nothing found.
   */
  public function tabcomplete() {
    return '';
  }

}