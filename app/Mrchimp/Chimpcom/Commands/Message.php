<?php

namespace Mrchimp\Chimpcom\Commands;

use App\User;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Models\Message as MessageModel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Send a message to another user
 */
class Message extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('message');
        $this->setDescription('Send a message to other users.');
        $this->setHelp('You can omit the MESSAGE keyword entirely and just start a command with @username.');
        $this->addUsage('@mrchimp Hey there!');
        $this->addRelated('mail');
        $this->addArgument(
            'content',
            InputArgument::IS_ARRAY | InputArgument::REQUIRED,
            'Usernames and message content. Usernames are any word that begins with an @.'
        );
    }

    /**
     * Run the command
     *
     * @todo only remove names from start of content - leave them in message
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = Auth::user();
        $content = $input->getArgument('content');
        $recipient_names = [];

        foreach ($content as $key => $word) {
            if (substr($word, 0, 1) === '@') {
                $recipient_names[] = substr($word, 1);
                unset($content[$key]);
            }
        }

        if (empty($recipient_names)) {
            $output->error('You need to tell me who to send that to. Begin usernames with an @ symbol - Twitter style.');

            return 1;
        }

        $body = implode(' ', $content);

        $success_count = 0;

        $output->write('Sending to...<br>');

        foreach ($recipient_names as $name) {
            $recipient = User::where('name', $name)->first();

            if (!$recipient) {
                $output->error(e($name) . ' ✘<br>');

                continue;
            }

            $message = new MessageModel();
            $message->message = $body;
            $message->recipient_id = $recipient->id;
            $message->author_id = $user->id;
            $message->has_been_read = false;
            $message->save();

            $output->alert(e($name) . ' ✔<br>');

            $success_count++;
        }

        $name_count = count($recipient_names);

        if ($name_count > 1) {
            if ($success_count === 0) {
                $output->error('No messages were sent.');
            } elseif ($success_count === $name_count) {
                $output->alert('All messages were sent!');
            } else {
                $success_percent = (($success_count / $name_count) * 100);
                $output->error('Sent messages with ~' . round($success_percent, 3) . '% success rate.');
            }
        } else {
            if ($success_count == 1) {
                $output->alert('Message sent!');
            } else {
                $output->error('Error sending message.');
            }
        }

        return 0;
    }
}
