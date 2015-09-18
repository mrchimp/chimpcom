<?php
/**
 * Wrap strings in spans
 */

namespace Mrchimp\Chimpcom;

use Auth;

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

  /**
   * Converts an dimensional array into an html table
   *
   * @param array   $list      the array to be converted
   * @param integer $cols      number of columns in table
   * @param boolean $sort_list if true list will be sorted alphabetically
   * @return string
   */
  static function listToTable(array $list, $cols = 1, $sort_list = false) {
    if (!is_array($list)) {
      trigger_error('That wasn\'t an array.', E_USER_NOTICE);
      return false;
    }

    $cols = ($cols < 1 ? 1 : $cols);

    if ($sort_list) sort($list);

    $num_of_items = count($list);
    $output_count = 0;
    $row_count = 1;
    $s = '<table>';

    while (isset($list[$output_count])) {
      //$s .= $row_count.'-';
      //$s .= $output_count.'-';
      $s .= ($row_count == 1 ? '<tr>' : '');
      $s .= '<td>'.$list[$output_count].'</td>';
      $s .= ($row_count == $cols ? '</tr>' : '');

      $row_count++;
      $row_count = $row_count % $cols;
      $output_count++;
    }

    $s .= '</table>';
    return $s;
  }


  /**
   * Output memories
   */
  static function memories($memories) {

    // Output memories
    $previous_name = '';
    $chunks = [];
    $output = '';
    $current_user = Auth::user();

    foreach ($memories as $memory) {
      if ($memory->name != $previous_name) {
        $output .= Format::listToTable($chunks, 5) . '<br>';
        $output .= Format::alert(e(ucwords($memory->name))) . '<br>';
        $chunks = [];
      }

      $hexid = Chimpcom::encodeId($memory['id']);

      // Memory ID
      $chunks[] = Format::grey($hexid, [
        'data-type' => 'autofill',
        'data-autofill' => "forget $hexid"
      ]);

      if ($memory->isMine()) {
        $chunks[] = Format::title($memory->user->name, []);
      } else {
        $chunks[] = Format::grey($memory->user->name, []);
      }

      // Public
      if ($memory->public) {
        $chunks[] = Format::alert('P', [
          'title' => 'Public: anyone can see this.',
          'data-type' => 'autofill',
          'data-autofill', "setpublic $hexid --private"
        ]);
      } else {
        $chunks[] = Format::grey('p', [
          'title' => 'Private: only you can see this',
          'data-type' => 'autofill',
          'data-autofill' => "setpublic $hexid"
        ]);
      }

      // Major
      $pos = strpos($memory->content,'#major');
      if ($pos !== false) {
        $chunks[] = $this->error('!', [
          'title' => 'Major! This is important! Take notice! Act now!'
        ]);
      } else {
        $chunks[] = '<span>&nbsp;</span>';
      }

      // Minor
      $pos = strpos($memory->content,'#minor');
      $minor = ($pos !== false);

      // Content
      if ($current_user->id == $memory->user->id) {
        $attrs = [
          'data-type' => 'autofill',
          'data-autofill' => "update $hexid {$memory['content']}"
        ];
      } else {
        $attrs = [];
      }

      if ($minor) {
        $chunks[] = Format::grey(Format::autoLink(e($memory->content)), $attrs);
      } else {
        $chunks[] = Format::style(Format::autoLink(e($memory->content)), '', $attrs);
      }

      $previous_name = $memory->name;

    }

    $output .= Format::listToTable($chunks, 5) . '<br>';

    if (count($memories) > 5) {
      $output .= '<br>' . count($memories) . ' memories found.';
    }

    return $output;
  }

  /**
   * Replace URLs in text with html links.
   *
   * @param  string $text
   * @return string
   */
  static function autoLink($text){
    $pattern  = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';
    $callback = create_function('$matches', '
      $url       = array_shift($matches);
      $url_parts = parse_url($url);

      $text = preg_replace("/^www./", "", $url_parts["host"]) . (isset($url_parts["path"]) ? "/..." : "");

      return sprintf(\'<a rel="nofollow" href="%s">%s</a>\', $url, $text);'
    );

    return preg_replace_callback($pattern, $callback, $text);
  }

}