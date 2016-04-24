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

    protected $title = 'Mail';
    protected $description = 'Read messages from other users. Use --sent to show messages that you have sent to others. Messages will be marked as read as soon as they are seen. Use --dont-read to prevent this.';
    protected $usage = 'mail [--all|-a] [--sent|-s] [--dont-read|-r] [--delete|-d &lt;message_id&gt;]';
    protected $see_also = 'message';

    /**
     * Run the command
     */
    public function process() {
        $user = Auth::user();

        // Delete messages
        if ($this->input->isFlagSet(['--delete', '-d'])) {
            $message_ids = array_slice($this->input->getWordArray(), 1);
            $result = MessageModel::where('recipient_id', $user->id)
                ->whereIn('id', $message_ids)
                ->delete();

            if ($result) {
              $this->response->alert('Message(s) deleted.');
            } else {
              $this->response->error('There was a problem.');
            }

            return;
        }

        $showAll = $this->input->isFlagSet(['--all', '-a']);
        $mailbox = ($this->input->isFlagSet(['--sent', '-s']) ? 'outbox' : 'inbox');

        $messages = MessageModel::where('recipient_id', $user->id)
            ->with('author', 'recipient')
            ->get();

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
