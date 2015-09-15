<?php 
/**
 * Read Reddit descretely
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Reddit as Api;
use Cache;

/**
  * Read Reddit descretely
 */
class Reddit extends AbstractCommand
{

  /**
   * Wastes time
   * reddit                              <frontpage>
   * reddit -r --reddit subreddit        <subreddit>
   * reddit -c --comments 12345 [12345]  <post/comment>
   * reddit -l --list                    <reddits>
   * reddit -s --self                    show self text
   */
  public function process() {
    $cache_time    = 10;
    $indent        = 5;
    $post_id       = $this->input->getWord(1);
    $comment_id    = $this->input->getWord(2);
    $show_selftext = $this->input->isFlagSet(array('-s', '--self'));

    $reddit = new Api();
    $reddit->show_selftext = $show_selftext;
    
    if ($show_selftext) {
      $this->response->say(Format::alert('Showing selftext.').'<br>');
    }
    
    if ($this->input->isFlagSet(array('-r', '--reddit'))) {
      $action = 'subreddit';
    } else if ($this->input->isFlagSet(array('-c', '--comments'))) {
      if ($comment_id !== false) {
        $action = 'comment';
      } else {
        $action = 'post';
      }
    } else if ($this->input->isFlagSet(array('-l', '--list'))) {
      $action = 'reddits';
    } else {
      $action = 'frontpage';
    }

    $html = $reddit->get($action, $post_id, $comment_id);
    $this->response->say($html);

    // @todo - Make this work
    // if ($this->user->isAdmin()) {
    //   $this->response->say('<br>'.($use_cache ? 'Using cache.' : 'Not using cache.'));
    // }
  }

}
