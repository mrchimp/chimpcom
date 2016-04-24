<?php
/**
 * Find a memory by its name
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Chimpcom;
use Mrchimp\Chimpcom\Models\Memory;

/**
 * Find a memory by its name or description
 */
class Find extends LoggedInCommand
{

    protected $title = 'Find';
    protected $description = 'Find a memory by its name or description.';
    protected $usage = 'find &lt;search_term&gt;';
    protected $example = 'find chimpcom';
    protected $see_also = 'save, show, forget, setpublic';

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
