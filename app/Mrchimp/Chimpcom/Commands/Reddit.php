<?php

/**
 * Read Reddit descretely
 */

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Cache;
use Mrchimp\Chimpcom\Reddit as Api;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Read Reddit descretely
 */
class Reddit extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('reddit');
        $this->setDescription('Wastes time.');
        $this->addArgument(
            'post',
            null,
            'Post ID'
        );
        $this->addArgument(
            'comment',
            null,
            'Comment ID'
        );
        $this->addOption(
            'reddit',
            'r',
            null,
            'Show posts from a given subreddit.'
        );
        $this->addOption(
            'comments',
            'c',
            null,
            'Show a given comment tree.'
        );
        $this->addOption(
            'list',
            'l',
            null,
            'List subreddits.'
        );
        $this->addOption(
            'self',
            's',
            null,
            'Show self-post text.'
        );
    }

    /**
     * Run the command
     *
     * Wastes time
     * reddit                              <frontpage>
     * reddit -r --reddit subreddit        <subreddit>
     * reddit -c --comments 12345 [12345]  <post/comment>
     * reddit -l --list                    <reddits>
     * reddit -s --self                    show self text
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cache_time    = 10;
        $indent        = 5;
        $post_id       = $input->getArgument('post');
        $comment_id    = $input->getArgument('comment');
        $show_selftext = $input->getOption('self');

        $reddit = new Api();
        $reddit->show_selftext = $show_selftext;

        if ($show_selftext) {
            $output->alert('Showing selftext.<br>');
        }

        if ($input->getOption('reddit')) {
            $action = 'subreddit';
        } elseif ($input->getOption('comments')) {
            if ($comment_id !== false) {
                $action = 'comment';
            } else {
                $action = 'post';
            }
        } elseif ($input->getOption('list')) {
            $action = 'reddits';
        } else {
            $action = 'frontpage';
        }

        $html = $reddit->get($action, $post_id, $comment_id);
        $output->write($html);

        return 0;
        // @todo - Make this work
        // if ($this->user->isAdmin()) {
        //   $output->write('<br>'.($use_cache ? 'Using cache.' : 'Not using cache.'));
        // }
    }
}
