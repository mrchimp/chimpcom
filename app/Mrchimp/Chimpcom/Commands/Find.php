<?php 
/**
 * Your basic common or garden Chimpcom function
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Chimpcom;
use Mrchimp\Chimpcom\Models\Memory;

/**
 * Your basic common or garden Chimpcom function
 */
class Find extends LoggedInCommand
{

    /**
     * Run the command
     */
    public function process() {
        $user = Auth::user();

        if (!$user->is_admin) {
            $this->error('No.');
            return;
        }

        $search_term = '%'.$this->input->getParamString().'%';
        $user_id     = $user->id;

        $memories = Memory::where('name', 'LIKE', $search_term)
                          ->orWhere('content', 'LIKE', $search_term)
                          ->get();

        if (count($memories) === 0){
            $this->response->error('I have no recollection of that.');
            return;
        }

        $this->response->say(Format::memories($memories));
    }

}