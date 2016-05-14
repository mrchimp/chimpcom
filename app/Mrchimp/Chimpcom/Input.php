<?php

/**
 * Chimpcom command input
 */

namespace Mrchimp\Chimpcom;

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
   * Class constructor
   * @param string $cmd_in Command as input by user
   * @param OptionCollection $specs Command line parser spec
   */
  public function __construct($cmd_in, $specs) {
    $specs = new OptionCollection;
  }
}
