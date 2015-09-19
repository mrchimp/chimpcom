<?php 
/**
 * Give details on the current user
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Format;

/**
 * Give details on the current user
 */
class Who extends AbstractCommand
{

  /**
   * Run the command
   */
  public function process() {
    if ($this->input->get(1) === 'are' && 
         ( $this->input->get(2) === 'you?' || 
           $this->input->get(2) === 'you' )) {
      $this->response->say('I am a sentient command line. Be afraid.');
      return true;
    }
    
    if ($this->input->get(1) != false && 
        $this->input->get(1) != 'am') {
      $this->say('No idea.');
      return false;
    }
    
    if ($this->input->get(1) === false ||
        ($this->input->get(1) === 'am' && 
        ( $this->input->get(2) === 'i' ||
          $this->input->get(2) === 'i?' )) ){
      
      $tbl = '<table>';

      if (Auth::check()){
        $user = Auth::user();
        $tbl .= '<tr><td width="150">' . 
                  Format::title('USERNAME') . 
                  '</td> 
                 <td>'.e($user->name).'</td></tr>
                 <tr><td>' . 
                  Format::title('USER ID') . 
                 '</td>
                 <td>'.$user->id.'</td></tr>
               <tr>
               </tr>';
      }

      $tbl .= '<tr><td width="150">' . 
                  Format::title('IP ADDRESS') . 
                '</td>
               <td>'.$_SERVER['REMOTE_ADDR'].'</td></tr>
               <tr><td>' . 
                  Format::title('USERAGENT') . 
                '</td>
               <td>'.$_SERVER['HTTP_USER_AGENT'].'</td></tr>
               </table>';

      $this->response->say($tbl);
      return true;
    } 
    
    $this->response->error('Who what?');
  }

}