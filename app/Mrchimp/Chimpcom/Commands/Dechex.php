<?php 
/**
 * Convert decimal to hexadecimal
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;

/**
 * Convert decimal to hexadecimal
 */
class Dechex extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        $values = $this->input->getParamArray();

        foreach ($values as &$value) {
            if (substr($value, 0, 4) === 'rgb(') {
                $value = substr($value, 4, -1);
                $chunks = explode(',', $value);
                foreach ($chunks as &$chunk) {
                    $chunk = dechex($chunk);
                }
                $value = '#'.implode('', $chunks);
                $value .= ' <span style="color:'.$value.'">███████</span>';
            } else {
                $value = strval(dechex((int)$value));
            }
        }

        $this->response->say(implode('<br>', $values));
    }

}