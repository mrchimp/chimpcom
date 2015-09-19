<?php
/**
 * Chimpcom command input
 */

namespace Mrchimp\Chimpcom;

use Illuminate\Http\Request;
use Mrchimp\Chimpcom\Models\Alias as ChimpcomAlias;

/**
 * Chimpcom command input
 */
class Input
{

  /**
   * The whole input string as provided by the user
   * @var string
   */
  private $cmd_in;

  /**
   * Array of all input split by spaces
   * @var array
   */
  private $input_array = [];

  /**
   * Input_array minus first element
   * @var array
   */
  private $param_array = [];

  /**
   * "--flag / -f" from input_array
   * @var array
   */
  private $flag_array = [];

  /**
   * "@names" from input_array
   * @var array
   */
  private $name_array = [];

  /**
   * "#tags" from input_array
   * @var array
   */
  private $tag_array = [];

  /**
   * "#tags" from input_array
   * @var array
   */
  private $word_array  = []; // everything left over from input_array

  /**
   * The command name. Essentially the first word of cmd_in, lowercased.
   * @var string
   */
  private $command;

  /**
   * Class constructor
   * @param string $cmd_in Command as input by user
   */
  public function __construct($cmd_in) {
    $this->sliceInput($cmd_in);
  }

  /**
   * Slices the string at every space into an array
   * Split out @names, -flags and #tags into seperate arrays
   * @param string $input The input from user
   */
  private function sliceInput($input) {
    $this->cmd_in = $input;

    $parts = explode(' ', trim($input));

    $parts[0] = $this->getAlias($parts[0]);

    $this->cmd_in = implode(' ', $parts);

    foreach ($parts as $key => $value) {
      $first_char = substr($value, 0, 1);

      if ($first_char == '-' && $value != '-' && $value != '--') {
        array_push($this->flag_array, $value);
      } else if ($first_char == '@' && $value != '@') {
        array_push($this->name_array, substr($value, 1));
      } else if ($first_char == '#' && $value != '#') {
        array_push($this->tag_array, substr($value, 1));
      } else {
        array_push($this->word_array, $value);
      }
      array_push($this->input_array, $value);
    }

    $this->param_array = array_slice($this->input_array, 1);
    $this->command = e(strtolower($parts[0]));
  }
  
  /**
   * Get the input text as an array split by spaces. Gets different things
   * depending on what is passed as $index.
   *
   * get() // Get whole input string
   * get(1) // Get first (non flag) word
   * 
   * @return string or false
   */ 
  public function get($index = null){
    switch (gettype($index)) {
      case 'string':
        break;
      case 'integer':
        if (!empty($this->input_array[$index])){
          return $this->input_array[$index];
        } else {
          return false;
        }
        break;
      default:
        return $this->cmd_in;
    }
  }

  /**
   * Get the *words* from input separated by spaces 
   * 
   * @return string or false
   */ 
  public function getWord($index){
    if (!empty($this->word_array[$index])){
      return $this->word_array[$index];
    } else {
      return false;
    }
  }

  /**
   * Get a string of the input with the first word removed
   */
  public function getParamString() {
    return implode(' ', array_slice($this->input_array, 1));
  }

  /**
   * Get all parameter parameters (i.e. not name, tag, flag).
   * @return array
   */
  public function getParamArray() {
    return $this->param_array;
  }

  /**
   * Returns true if the flag is set.
   *
   * @return boolean
   */
  public function isFlagSet(array $flagnames) {
    foreach ($flagnames as $flag) {
      if (in_array($flag, $this->flag_array)) {
        return true;
      }
    }
    return false;
  }

  /**
   * Get the command name
   * @return string Command name
   */
  public function getCommand() {
    return $this->command;
  }

  /**
   * Look up a command alias
   */
  private function getAlias($cmd) {
    $alias = ChimpcomAlias::where('name', $cmd)->take(1)->first();

    return ($alias ? $alias->alias : $cmd);
  }

  /**
   * Get full input string.
   * @return string Command input
   */
  public function getInput() {
    return $this->cmd_in;
  }

  /**
   * Get tags from input
   * @return array Array of tags
   */
  public function getTags() {
    return $this->tag_array;
  }
}