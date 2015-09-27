<?php 
/**
 * Triggers action_forget
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Session;
use Mrchimp\Chimpcom\Chimpcom;
use Mrchimp\Chimpcom\Models\Memory;

/**
 * Triggers action_forget
 * @action forget
 */
class Forget extends LoggedInCommand
{

  /**
   * Run the command
   */
  public function process() {
    $user = Auth::user();
    $mem_id = $this->input->get(1);

    if ($mem_id == 'everything' || $mem_id == 'all') {
      $this->response->say('Where am I? Who are you? WHAT THE HELL\'S GOING ON?!');
      return;
    }

    if ($mem_id === false) {
      $this->response->error('What do you want me to forget? I need an id.');
      return;
    }

    $ids = [];
    foreach($this->input->getParamArray() as $id) {
      $ids[] = Chimpcom::decodeId($id);
    }

    $data = $ids;
    $data[] = $user->id;

    $memories = Memory::where('user_id', $user->id)
                      ->whereIn('id', $ids)->get();

    // $memories = \R::find(
    //   'memory', 
    //   'id IN (' . implode(',', array_fill(0,count($ids),'?')) . ') AND user = ?',
    //   $data
    // );
    
    if (empty($memories)) {
      $this->response->error('Couldn\'t find that memory or it\'s not yours to forget.');
      return;
    }

    $outs = [];
    foreach ($memories as $memory) {
      $outs[] = e($memory->name) . ': ' . e($memory->content);
    }
    $output = implode('<br>', $outs);

    $this->response->title('Are you sure you want to forget these memories?<br>');
    $this->response->say($output);
    
    Session::set('forget_id', $ids);
    $this->setAction('forget');
  }

}