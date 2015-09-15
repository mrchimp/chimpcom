<?php
/**
 * Wrap strings in spans
 */

namespace Mrchimp\Chimpcom;

/**
 * Wrap strings in spans
 */
class Format
{
 
  /**
   * Wrap text in a span with attributes.
   * 
   * @param  string $str   The text to wrap
   * @param  string $class The wrapping span's class
   * @param  array  $attr  An array of HTML attributes for the span
   * @return string        The wrapped content.
   */
  static function style($str, $class, array $attr = array()) {
    if (!empty($attr)) {
      $bits = array();

      foreach ($attr as $key => $value) {
        $bits[] = "$key = \"$value\"";
      }

      $attr_str = implode(' ', $bits);
    } else {
      $attr_str = '';
    }

    $str = "<span class=\"$class\" $attr_str>$str</span>";

    return $str;
  }

  /**
   * Append a red error to the output buffer.
   * 
   * @param  string $str   The text to wrap
   * @param  array  $attr  An array of HTML attributes for the span
   * @return string        The wrapped content.
   */
  static function error($str, array $attr = array()){
    return self::style($str, 'red_highlight', $attr);
  }
  
  /**
   * Append a red error to the output buffer.
   * 
   * @param  string $str   The text to wrap
   * @param  array  $attr  An array of HTML attributes for the span
   * @return string        The wrapped content.
   */
  static function grey($str, array $attr = array()){
    return self::style($str, 'grey_text', $attr);
  }

  /**
   * Append a green alert to the output buffer.
   * 
   * @param  string $str   The text to wrap
   * @param  array  $attr  An array of HTML attributes for the span
   * @return string        The wrapped content.
   */
  static function alert($str, array $attr = array()) {
    return self::style($str, 'green_highlight', $attr);
  }

  /**
   * Append a blue title to the output buffer.
   * 
   * @param  string $str   The text to wrap
   * @param  array  $attr  An array of HTML attributes for the span
   * @return string        The wrapped content.
   */
  static function title($str, array $attr = array()) {
    return self::style($str, 'blue_highlight', $attr);
  }
}