<?php 
/**
 * Flip a coin
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;

/**
 * Flip a coin
 */
class Coin extends AbstractCommand
{

    /**
     * Run the command
     */
    public function process() {
        $this->response->say((rand(0,1) ? 'Heads' : 'Tails'));
    }

}