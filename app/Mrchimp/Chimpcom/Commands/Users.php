<?php 
/**
 * Get a list of users
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use App\User;

/**
 * Get a list of users
 */
class Users extends AdminCommand
{

  /**
   * Run the command
   */
  public function process() {
    $q = ($this->input->get(1) !== false ? $this->input->get(1) : '');

    $users = User::get();

    $this->response->say('<table><tr><td>');
    $this->response->title('id');
    $this->response->say('</td><td>');
    $this->response->title('Username');
    $this->response->say('</td></tr>');

    foreach($users as $user){
      $this->response->say('<tr><td>'.$user['id'].'</td>
                   <td>'.$user->name.'</td></tr>');
    }

    $this->response->say('</table>');
  }

}