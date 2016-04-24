<?php
/**
 * Send a message to another user
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use App\User;
use Mrchimp\Chimpcom\Chimpcom;
use Mrchimp\Chimpcom\Models\Message as MessageModel;

/**
 * Send a message to another user
 */
class Message extends LoggedInCommand
{
    protected $title = 'Message';
    protected $description = 'Send a message to other users.';
    protected $usage = 'message &lt;username[s]&gt; &lt;message&gt;';
    protected $example = 'message @mrchimp Hey there!';
    protected $see_also = 'mail';

    /**
     * Run the command
     */
    public function process() {
        $user = Auth::user();
        $success_count = 0;
        $recipient_names = $this->input->getNames();

        if (empty($recipient_names)) {
            $this->response->error('You need to tell me who to send that to. Begin usernames with an @ symbol - Twitter style.');
            return false;
        }

        foreach ($recipient_names as $name) {
            $recipient = User::where('name', $name)->first();

            if ($recipient) {
                $message = new MessageModel();
                $message->message = $this->input->getParamString();
                $message->recipient_id = $recipient->id;
                $message->author_id = $user->id;
                $message->save();

                $this->response->say('Sent message to ' . e($name) . '.<br>');

                $success_count++;
            } else {
                $this->response->error('There is no user called ' . e($name) . '. Try using the USERS command.<br>');
            }
        }

        // Report result
        $name_count = count($recipient_names);

        if ($name_count > 1) {
            $success_percent = (($success_count / $name_count) * 100);
            if ($success_percent < 1) {
                $this->response->error('No messages were sent.');
            } else if ($success_percent > 99) {
                $this->response->alert('All messages were sent!');
            } else {
                $this->response->error('Sent messages with '.$success_percent.'% success rate.');
            }
        } else {
            if ($success_count == 1) {
                $this->response->alert('Message sent!');
            } else {
                $this->response->error('Error sending message.');
            }
        }
    }

}
