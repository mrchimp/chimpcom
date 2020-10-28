<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Message;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Your basic common or garden Chimpcom command
 */
class Hi extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('hi');
        $this->setDescription('Displays a welcome message.');
        $this->setHelp('There are no additional options for this command');
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
        if (Auth::check()) {
            $user = Auth::user();
            $output->write(Format::title('Welcome back, ' . e($user->name) . '.'));

            $messages = Message::where('recipient_id', $user->id)
                ->where('has_been_read', false)
                ->get();

            if (count($messages) > 0) {
                $output->write('<br>');
                $output->write('You have ' . count($messages) . ' new message' .
                    (count($messages) > 1 ? 's' : '') .
                    '. Type <code>mail</code> to read. ');

                if (count($messages) > 10) {
                    $output->write('Aren\'t you popular! ');
                }
            }
        } else {
            $output->write(Format::title('Chimpcom ' . Chimpcom::getVersion()), true);
            $output->write('Go ahead');
        }

        return 0;
    }
}
