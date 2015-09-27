<?php
/**
 * Save the description for a new project
 */

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Session;
use App\User;
use Illuminate\Http\Request;
use Mrchimp\Chimpcom\Booleanate;
use Mrchimp\Chimpcom\Commands\LoggedInCommand;
use Mrchimp\Chimpcom\Models\Memory;

/**
 * Save the description for a new project
 * @action normal
 */
class Forget extends LoggedInCommand
{

    protected $log_this = false;

    /**
     * Run the command
     */
    public function process() {
        $user = Auth::user();
        
        if (Booleanate::isAffirmative($this->input->get(0))) {
          $ids = Session::get('forget_id');

          $memories = Memory::where('user_id', $user->id)
                            ->whereIn('id', $ids)
                            ->delete();

          $this->setAction();
          $this->response->alert((count($ids) > 0 ? 'Memories' : 'memory') .' forgotten: #' . implode(', #', $ids));
          Session::forget('forget_id');
          return;
        }

        if (Booleanate::isNegative($this->input->get(0))) {
          $this->response->say('Action aborted.');
        } else {
          $this->response->say('Whatever.');
        }

        $this->setAction();
        Session::forget('forget_id');
    }

}