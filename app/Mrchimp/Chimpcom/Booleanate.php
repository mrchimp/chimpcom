<?php
/**
 * Check if a string is affirmative or negative.
 */

namespace Mrchimp\Chimpcom;

/**
 * Check if a string is affirmative or negative.
 */
class Booleanate
{

  /**
   * These words count as affirmative
   * @var array
   */
  private static $yes_words = array(
    'affirmative',
    'ja',
    'ok',
    'oui',
    'roger',
    'sure',
    'y',
    'ya',
    'yea',
    'yeah',
    'yes',
    'yup',
    'yep',
    'yar',
    'yarr',
    'yarrr',
    'yeppers'
  );
  
  /**
   * These words count as negative
   * @var array
   */
  private static $no_words = array(
    'abort',
    'cancel',
    'n',
    'negative',
    'no',
    'non',
    'nope',
    'nein'
  );

  /**
   * Returns true if parameter is yes-like
   * 
   * @param  string  $val The value to evaluate.
   * @return boolean True if $val is affirmative
   */
  public static function isAffirmative($val) {
    return in_array(strtolower($val), self::$yes_words);
  }

  /**
   * Returns true if parameter is no-like
   * @param  string  $val The value to evaluate.
   * @return boolean True if $val is no-like
   */
  public static function isNegative($val) {
    return in_array(strtolower($val), self::$no_words);
  }

}