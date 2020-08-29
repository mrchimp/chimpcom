<?php

/**
 * Get Chimpcom statistics
 */

namespace Mrchimp\Chimpcom\Commands;

use App\User;
use Auth;
use Mrchimp\Chimpcom\Models\Alias as ChimpcomAlias;
use Mrchimp\Chimpcom\Models\Feed;
use Mrchimp\Chimpcom\Models\Memory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Get Chimpcom statistics
 */
class Stats extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('stats');
        $this->setDescription('Show Chimpcom statistics.');

        $this->addArgument(
            'username',
            null,
            'User to show stats for. If not set, total stats will be shown.'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');

        if ($username) {
            // Individual user's stats
            $output->write("Finding stats for user: $username<br>");

            $user = User::where('name', $username)->first();

            if (!$user) {
                $output->error('That username does not exist.');

                return 1;
            }

            $memory_count = $user->memories()->count();
            $feed_count = $user->feeds()->count();
        } else {
            // All users
            $user_count = User::count();
            $memory_count = Memory::count();
            $feed_count = Feed::count();
            $output->write("Users: $user_count<br>");
        }

        $output->write("Memories: $memory_count<br>");
        $output->write("Feeds: $feed_count<br>");

        return 0;
    }
}
