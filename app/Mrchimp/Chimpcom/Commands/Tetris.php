<?php 
/**
 * Show a random tetrino
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Chimpcom;

/**
 * Show a random tetrino
 */
class Tetris extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        $tetrinos = [
          '&#x25A0;&#x25A0;&#x25A0;&#x25A0;',                     // Line
          '&#x25A0;<br>&#x25A0;<br>&#x25A0;&#x25A0;',             // J
          '&nbsp;&#x25A0;<br>&nbsp;&#x25A0;<br>&#x25A0;&#x25A0;', // L
          '&#x25A0;&#x25A0;<br>&#x25A0;&#x25A0;',                 // Square
          '&nbsp;&#x25A0;<br>&#x25A0;&#x25A0;&#x25A0;',           // T
          '&#x25A0;&#x25A0;<br>&nbsp;&#x25A0;&#x25A0;',           // Z
          '&nbsp;&#x25A0;&#x25A0;<br>&#x25A0;&#x25A0;'            // S
        ];
        
        $this->response->say('<div class="tetris">' . $tetrinos[rand(0, count($tetrinos) - 1)] . '</div>');
    }

}