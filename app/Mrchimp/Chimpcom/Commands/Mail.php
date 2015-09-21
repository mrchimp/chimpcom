<?php 
/**
 * View messages sent by other users
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Chimpcom;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Message as MessageModel;

/**
 * View messages sent by other users
 */
class Mail extends LoggedInCommand
{

    /**
     * Run the command
     */
    public function process() {
        $user = Auth::user();

        // @todo - allow deleting messages
        // Delete messages
        // if ($this->isFlagSet(array('--delete', '-d'))) {
          // if ($this->isFlagSet(array('--all', 'a'))) {
            // $result = $messenger->deleteMessage('all');      
          // } else if ($this->inputArray(1) != false) {
            // $result = $messenger->deleteMessage($this->inputArray(1));
          // }
          // if ($result) {
            // $this->alert('Message(s) deleted.');
          // } else {
            // $this->error('There was a problem.');
          // }
          // return $result;
        // }
        
        $showAll = $this->input->isFlagSet(['--all', '-a']);
        $mailbox = ($this->input->isFlagSet(['--sent', '-s']) ? 'outbox' : 'inbox');
        
        $messages = MessageModel::where('recipient_id', $user->id)->get();
          
        if (count($messages) === 0) {
            $this->response->say('No messages');
            return;
        }

        $this->response->say(Format::messages($messages));
        
        if (!$this->input->isFlagSet(['-r', '--dont-read'])) {
            foreach ($messages as $message) {
                $message->has_been_read = true;
                $message->save();
            }
        }
    }

}