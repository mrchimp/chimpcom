<?php

/**
 * Read RSS feeds
 */

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Feed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Read RSS feeds
 */
class Rss extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('rss');
        $this->setDescription('Read RSS feeds.');
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
            $output->setResponseCode(401);
            return 1;
        }

        $user = Auth::user();
        $subcommand = $input->getArgument('subcommand');
        $name = $input->getArgument('name');
        $url = $input->getArgument('url');

        if ($subcommand === 'add') {
            if (!$name) {
                $output->error('You need to provide a name.');
                $output->setResponseCode(422);
                return 2;
            }

            if (!$url) {
                $output->error('You need to provide a URL.');
                $output->setResponseCode(422);
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

            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                $output->error('There was a problem with that.');
                $output->setResponseCode(422);
                return 3;
            }

            $user->feeds()->create($data);
            $output->alert('Ok.');

            return 0;
        }

        if ($subcommand === 'list') {
            $feeds = $user->feeds;

            if (count($feeds) === 0) {
                $output->error('No feeds. use `RSS ADD ...`');
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
            if (!$name) {
                $output->error('You must provide a feed name.');
                $output->setResponseCode(422);
                return 5;
            }

            $feed = Feed::where('name', $name)
                ->where('user_id', $user->id)
                ->first();

            if (!$feed) {
                $output->error(e('Could not find feed or it isn\'t yours to remove.'));
                $output->setResponseCode(422);
                return 6;
            }

            $feed->delete();

            $output->alert('Feed removed.');

            return 0;
        }

        // ============= Get feeds ============================
        if (!$user->feeds) {
            $output->write(e('Couldn\'t get feed list.'));
            return 7;
        }

        foreach ($user->feeds as $feed) {
            $output->write(Format::feed($feed->getFeed()));
        }

        return 0;
    }
}
