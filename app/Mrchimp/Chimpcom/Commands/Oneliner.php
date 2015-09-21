<?php 
/**
 * Create a new witty oneliner
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Chimpcom;
use Mrchimp\Chimpcom\Models\Oneliner as OnelinerModel;

/**
 * Create a new witty oneliner
 */
class Oneliner extends AdminCommand
{

    /**
     * Run the command
     */
    public function process() {
        $user = Auth::user();
        
        $oneliner = new OnelinerModel();
        $oneliner->command = $this->input->get(1);
        $oneliner->response = implode(' ', array_slice($this->input->getParamArray(), 1));
        $oneliner->save();
        
        $this->response->alert('Ok.');
    }

}