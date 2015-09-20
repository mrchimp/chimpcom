<?php 
/**
 * Convert hexadecimal to decimal
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;

/**
 * Convert hexadecimal to decimal
 */
class Hexdec extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        // We have a list
        if ($this->input->get(2) !== false) {
            $this->response->say('multiple<br>');
            $values = $this->input->getParamArray();

            foreach ($values as $value){
                $value = hexdec($value);
            }

            $this->response->say(implode(' ', $values));
            return true;
        }

        $input = $this->input->get(1);

        // we've got a single item
        if ($input) {
            $this->response->say(hexdec($input));
            return;
        }

        $tags = $this->input->getTags();

        // Try the tags
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $tag = '#'.$tag;
                $length = strlen($tag);

                // FFF
                if ($length == 4 && substr($tag, 0, 1) == '#') {
                    $output = 'rgb(' . hexdec(substr($tag, 1, 1) . substr($tag, 1, 1)) . ', ' .
                                       hexdec(substr($tag, 2, 1) . substr($tag, 2, 1)) . ', ' .
                                       hexdec(substr($tag, 3, 1) . substr($tag, 3, 1)) . ') ';
                    $output .= '<span style="color:'.$output.'">███████</span>';
                    $this->response->say($output . '<br>');
                }

                // FFFFFF
                if ($length == 7 && substr($tag, 0, 1) == '#') {
                    $output = 'rgb(' . hexdec(substr($tag, 1, 2)) . ', ' .
                                       hexdec(substr($tag, 3, 2)) . ', ' .
                                       hexdec(substr($tag, 5, 2)) . ')';
                    $output .= '<span style="color:'.$output.'">███████</span>';
                    $this->response->say($output . '<br>');
                }
            }
            return;
        }

        $this->response->error('Nothing to convert.');

    }

}