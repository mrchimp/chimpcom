<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Message as MessageModel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * View messages sent by other users
 */
class Mail extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('mail');
        $this->setDescription('Read messages from other users.');
        $this->setHelp('Use --sent to show messages that you have sent to others. ' .
            'Messages will be marked as read as soon as they are seen. ' .
            'Use --dont-read to prevent this.');
        $this->addRelated('message');
        $this->addOption(
            'sent',
            's',
            null,
            'Show sent messages.'
        );
        $this->addOption(
            'read',
            'r',
            null,
            'Mark messages as read.'
        );
        $this->addOption(
            'delete',
            'd',
            null,
            'Delete messages by ID.'
        );
        $this->addArgument(
            'delete_ids',
            InputArgument::IS_ARRAY,
            'IDs of messages to delete. For use with the --delete flag.'
        );
    }

    /**
     * Run the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));

            return 1;
        }

        $user = Auth::user();

        // Delete messages
        if ($input->getOption('delete')) {
            $message_ids = $input->getArgument('delete_ids');

            if (empty($message_ids)) {
                $output->error('No message IDs given.');

                return 2;
            }

            $result = MessageModel::where('recipient_id', $user->id)
                ->whereIn('id', $message_ids)
                ->delete();

            if ($result) {
                $output->alert('Message(s) deleted.');
            } else {
                $output->error('There was a problem.');
            }

            return 0;
        }

        $mailbox = $input->getOption('sent') ? 'outbox' : 'inbox';

        $messages = MessageModel::where('recipient_id', $user->id)
            ->with('author', 'recipient')
            ->get();

        if ($input->getOption('read')) {
            foreach ($messages as $message) {
                $message->has_been_read = true;
                $message->save();
            }
        }

        if (count($messages) === 0) {
            $output->write('No messages');
            return 0;
        }

        $output->write(Format::messages($messages));
        return 0;
    }
}
