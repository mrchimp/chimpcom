<?php 
/**
 * Make memories public/private
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Models\Memory;
use Mrchimp\Chimpcom\Chimpcom;

/**
 * Make memories public/private
 */
class Setpublic extends LoggedInCommand
{

    /**
     * Run the command
     */
    public function process() {
        $id = $this->input->get(1);

        if ($id === false) {
            $this->response->error('I\'m going to need an ID.');
            return;
        }

        $id = Chimpcom::decodeId($id);
        $memory = Memory::find($id);

        if (!$memory) {
            $this->response->error('That memory doesn\'t exist.');
            return;
        }

        if (!$memory->isMine()) {
            $this->response->error('That isn\'t your memory to change.');
            return;
        }

        $memory->public = ($this->input->isFlagSet(['--private']) ? 0 : 1);
        $memory->save();

        $this->response->alert('Ok.');
    }

}