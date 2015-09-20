<?php
/**
 * Main Chimpcom object.
 */

namespace Mrchimp\Chimpcom;

use Illuminate\Http\Request;
use Mrchimp\Chimpcom\Models\Shortcut;
use Mrchimp\Chimpcom\Models\Oneliner;
use Mrchimp\Chimpcom\Commands\UnknownCommand;
use DB;
use Session;


/**
 * Main Chimpcom object.
 * Any wrappers or ports should be based around this class.
 */
class Chimpcom
{

  /**
   * Chimpcom verion number
   */
  const VERSION = 'v6.0b';

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
    // 'candyman',
    // 'done',
    // 'forget',
    // 'login',
    // 'new_project',
    'password',
    // 'project_rm',
    'register',
    'register2',
    'register3'
  );

  /**
   * Available Chimpcom commands. Used to compare input against for security.
   * @var array
   */
  private static $available_commands = array(
    'addshortcut',
    'alias',
    'are',
    // 'ascii',
    'base64decode',
    'base64encode',
    // 'candyman',
    // 'cd',
    // 'charmap',
    // 'chpass',
    // 'coin',
    // 'config',
    // 'credits',
    // 'date',
    'deal',
    // 'dechex',
    // 'do',
    // 'does',
    // 'done',
    // 'echo',
    // 'email',
    // 'episode',
    // 'feeds',
    // 'files',
    // 'find',
    // 'forget',
    // 'go',
    // 'hash',
    // 'help',
    // 'hexdec',
    // 'hi',
    // 'hosttoip',
    // 'iptohost',
    // 'lipsum',
    'login',
    'logout',
    // 'look',
    'magiceightball',
    // 'mail',
    // 'man',
    // 'message',
    // 'monkeys',
    // 'oneliner',
    // 'phpinfo',
    // 'priority',
    // 'project',
    // 'projects',
    'reddit',
    'register',
    'save',
    // 'scale',
    // 'setpublic',
    'show',
    'shortcuts',
    // 'stats',
    'styles',
    // 'tea',
    // 'tetris',
    // 'thing',
    // 'this',
    // 'todo',
    // 'uname',
    // 'users',
    // 'version',
    // 'weather',
    'whoami',
    'who'
  );

  /**
   * Take an input string and return a resopnse array
   * @param  string           $input the user input string.
   * @return ChimpcomResponse        Response object
   */
  public function respond($input) {
    $this->input = new Input($input);

    // Catch "@username foo" messages shorthand
    if (substr($input, 0, 1) === '@') {
      $input = "message $input";
    }

    $this->cmd_in = $input;

    if ($input === 'clearaction') {
      $this->setAction('normal');
      $command = new BypassCommand();
      return $command->run('clearaction', Format::alert('Ok.'));
    }

    // Do check for non-normal action
    $action = $this->getAction();

    if ($action !== 'normal') {
      if (in_array($action, self::$available_actions)) {
        try {
          $command_name = "Mrchimp\Chimpcom\Actions\\".ucfirst($action);
          $command = new $command_name;
          return $command->run($this->input);
        } catch (Exception $e) {
          trigger_error('Invalid action: '.$action);
          $this->setAction();
          $response = new Response();
          $response->say(htmlspecialchars($input));
          $response->setCmdOut(Format::error('Invalid action. This should not have happened.'));
          return $response;
        }
      } else {
        $response = new Response();
        $response->error('Invalid action: '.htmlspecialchars($action));
        return $response;
      }
    }

    // Check for shortcuts?
    $shortcut = Shortcut::where('name', $this->input->get(0))->take(1)->first();
    
    if (count($shortcut) > 0) {
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
    
    // Do we have a witty oneliner?
    $oneliner = Oneliner::where('command', $this->input->getCommand())
                  ->orderBy(DB::raw('RAND()'))
                  ->take(1)
                  ->get();

    if (count($oneliner) > 0) {
      $response = new Response;
      $response->say($this->input->getInput());
      $response->setCmdOut($oneliner->response);

      return $response;
    }
    
    // Normal command?
    if (in_array($this->input->getCommand(), self::$available_commands)) {
      try {
        $command_name = "Mrchimp\Chimpcom\Commands\\".ucfirst($this->input->getCommand());
        $command = new $command_name;
        return $command->run($this->input);
      } catch (FatalErrorException $e) {
        $response = new Response;
        $response->say($this->input->getInput());
        $response->setCmdOut('Failed to load command.');
        return $response;
      }
    }

    // I give up
    $command = new UnknownCommand();
    return $command->run($this->input);
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

  // Convert integer ID to front-facing id
  static function encodeId($id) {
    return dechex($id);
  }

  static function decodeId($id) {
    return hexdec($id);
  }
}