<?php
/**
 * Chimpcom command response
 */

namespace Mrchimp\Chimpcom;

use Auth;
use Validator;

/**
 * Chimpcom command response
 */
class Response
{

  /**
   * Response array. This is what will eventually be returned.
   * @var array
   */
  private $out = array(
    'cmd_out'     => '',
    'show_pass'   => false,
    'cmd_fill'    => '',
    'log'         => '',
    'user'        => array(
      'id'     => 0,
      'name'   => 'Guest'
    )
  );

  /**
   * Class constructor
   */
  function __construct() {
    $this->getUserDetails();
  }

  /**
   * Append some text to the output.
   * @param string $str The text to append
   */
  public function say($str) {
  	$this->out['cmd_out'] .= $str;
  }

  /**
   * Format a string and then say() it
   * @param  string $str
   */
  public function error($str) {
    $this->say(Format::error($str));
  }

  /**
   * Format a string and then say() it
   * @param  string $str
   */
  public function alert($str) {
    $this->say(Format::alert($str));
  }

  /**
   * Format a string and then say() it
   * @param  string $str
   */
  public function grey($str) {
    $this->say(Format::grey($str));
  }

  /**
   * Format a string and then say() it
   * @param  string $str
   */
  public function title($str) {
    $this->say(Format::title($str));
  }

  /**
   * Get output in JSON format
   * @return string JSON Command response
   */
  public function getJson() {
  	return json_encode($this->out);
  }

  /**
   * Return the output HTML only.
   * @return string Response HTML
   */
  public function getTextOutput() {
  	return $this->out['cmd_out'];
  }

  /**
   * Change client input to password input
   * @param  boolean $on If true, use password input. Otherwise normal text input.
   */
  public function usePasswordInput($on = true) {
  	$this->out['show_pass'] = !!$on;
  }

  /**
   * Output a string to the client's console
   * @param  string $str
   */
  public function log($str) {
  	$this->out['log'] .= $str;
  }

  /**
   * Set the cmd_out (the string output to the terminal).
   * You probably don't need to call this. You probably want say().
   * @param string $str The new cmd_out
   */
  public function setCmdOut($str) {
    $this->out['cmd_out'] = htmlspecialchars($str);
  }

  /**
   * Insert text into the command input.
   *
   * @param string $str The string to be inserted
   */
  public function cFill($str) {
    $this->out['cmd_fill'] .= $str;
  }

  /**
   * Get user details from session. This only needs to be called after user logs in/out.
   */
  public function getUserDetails() {
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
   * Redirect the browser
   */
  public function redirect($url) {
    $this->out['redirect'] = $url;
  }

  /**
   * Open a new browser window
   */
  public function openWindow($url, $specs = ''){
    $this->out['openWindow'] = $url;
    $this->out['openWindowSpecs'] = $specs;
  }

}
