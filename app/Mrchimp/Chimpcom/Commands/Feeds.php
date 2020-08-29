<?php

/**
 * Read RSS feeds
 */

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Feed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Read RSS feeds
 */
class Feeds extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('feeds');
        $this->setDescription('Reads RSS feeds.');
        $this->addUsage('');
        $this->addUsage('add example http://example.com/rss_url.xml');
        $this->addUsage('list');
        $this->addUsage('remove example');
        $this->addArgument(
            'subcommand',
            null,
            'add, list or remove. If not provided, feeds are displayed'
        );
        $this->addArgument(
            'name',
            null,
            'An alias for a feed.'
        );
        $this->addArgument(
            'url',
            null,
            'An RSS feed URL.'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {


        if (!Auth::check()) {
            $output->error('You must be logged in to use this command.');
            return 1;
        }

        $user = Auth::user();

        $subcommand = $input->getArgument('subcommand');
        $name = $input->getArgument('name');
        $url = $input->getArgument('url');

        if ($subcommand === 'add') {
            if (!$url) {
                $output->error('You need to provide a url.');
                return 2;
            }

            $data = [
                'name' => $name,
                'url' => $url,
            ];

            $rules = [
                'name' => 'required|string|min:1',
                'url' => 'required|active_url',
            ];

            if (!$this->validateOrDie($data, $rules)) {
                return 3;
            }

            $feed = new Feed($data);
            $user->feeds()->save($feed);
            $output->alert('Ok');

            return 0;
        }


        if ($subcommand === 'list') {
            $feeds = $user->feeds;

            if (count($feeds) === 0) {
                $output->error('No feeds. use `FEED ADD ...`');
                return 4;
            }

            foreach ($feeds as $feed) {
                $output->write(
                    Format::title(e($feed->name)) . ': ' . e($feed->url) . '<br>'
                );
            }

            return 0;
        }


        if ($subcommand == 'remove') {
            if ($name === false) {
                $output->error('You must provide a feed name.');
                return 5;
            }

            $feed = Feed::where('name', $name)
                ->where('user_id', $user->id)
                ->first();

            if (!$feed) {
                $output->error('Could not find feed or it isn\'t yours to remove.');
                return 6;
            }

            $result = $feed->delete();

            if ($result) {
                $output->alert('Feed removed.');
            } else {
                $output->error('Problem removing feed.');
            }

            return 0;
        }


        // ============= Get feeds ============================
        if (!$user->feeds) {
            $output->write('Couldn\'t get feed list.');
            return 7;
        }

        foreach ($user->feeds as $feed) {
            $the_feed = $feed->getFeed(); // Well shit this is getting confusing

            $output->write(Format::feed($the_feed));
        }

        return 0;
    }
}
