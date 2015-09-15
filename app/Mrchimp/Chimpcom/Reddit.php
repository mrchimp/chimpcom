<?php
/**
 * Get data from Reddit.
 */

namespace Mrchimp\Chimpcom;

use Illuminate\Support\Facades\View;
use Mrchimp\Chimpcom\Format as Format;
use Cache;

/**
 * Get data from Reddit.
 */
class Reddit
{
  /**
   * Time in seconds(?) that responses are cahced.
   * @var integer
   */
  public $cache_time    = 10;

  /**
   * Amount of spaces to indent nested content by.
   * @var integer
   */
  public $indent        = 5;

  /**
   * Whether or not to show posts' text content.
   * @var boolean
   */
  public $show_selftext = true;
  
  /**
   * ID of the reddit post
   * @var integer
   */
  private $post_id;

  /**
   * ID of the reddit comment
   * @var integer
   */
  private $comment_id;

  /**
   * Get a JSON file from Reddit.
   * @param  string $url URL to get
   * @return string      JSON response
   */
  private function get_content($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
  }

  /**
   * [get description]
   * @param  string  $action     Type of reddit data to get.
   *                             Possible values are subreddit, post, comment,
   *                             reddits, frontpage(default).
   * @param  integer $post_id    Post ID
   * @param  integer $comment_id Comment ID
   * @return string              
   */
  public function get($action = 'frontpage', $post_id = null, $comment_id = null) {
    $this->post_id = $post_id;
    $this->comment_id = $comment_id;

    switch ($action) {
      case 'subreddit':
        $remote_file = "http://www.reddit.com/r/{$this->post_id}/.json";
        $cache_file  = $action;
        break;
      case 'post':
        $remote_file = "http://www.reddit.com/comments/{$this->post_id}/.json";
        $cache_file  = "comments_{$this->post_id}";
        break;
      case 'comment':
        $remote_file = "http://www.reddit.com/comments/{$this->post_id}/{$this->comment_id}.json";
        $cache_file  = "comments_{$this->post_id}_{$this->comment_id}";
        break;
      case 'reddits':
        $remote_file = 'http://www.reddit.com/reddits.json';
        $cache_file  = "reddits";
        break;
      case 'frontpage':
      default:
        $remote_file = 'http://www.reddit.com/.json';
        $cache_file  = "root";
    }

    if ($this->show_selftext) {
      Format::alert('showing selftext<br>');
    }

    if (Cache::has($cache_file)) {
      // echo 'Using cache: '.$cache_file;
      $content = Cache::get($cache_file);
    } else {
      // echo 'Getting remote file: '.$remote_file;
      $content = $this->get_content($remote_file);
      Cache::put($cache_file, $content, $this->cache_time);
    }

    $file = json_decode($content, true);
    
    if (isset($file['kind'])) {
      return $this->renderReddit($file);
    } else if (gettype($file) == 'array') {
      $output = '';

      foreach ($file as $node) {
        $output .= $this->renderReddit($node);
      }

      return $output;
    } else {
      return 'Something unexpected happened: '.$file.'. Try again?';
    }
  }


  /**
   * Render JSON Reddit content recursively
   * @param  object  $node  Decoded Reddit JSON object
   * @param  integer $depth Nesting amount
   * @return string         Rendered output
   */
  function renderReddit($node, $depth = 0) {
    View::share([
      'indent'        => $this->indent,
      'post_id'       => $this->post_id,
      'comment_id'    => $this->comment_id,
      'show_selftext' => $this->show_selftext
    ]);
    return view('reddit.reddit', [
      'node'  => $node,
      'depth' => $depth,
      // 'indent' => $this->indent
    ])->render();
    // switch ($node['kind']) {
    //   case 'Listing':
    //     // return $this->renderListing($node['data'], $depth); 
    //     return view('reddit.listing', [$node['data'], $depth]);
    //   case 't5': // subreddit
    //     return $this->renderSubreddit($node['data'], $depth);
    //   case 't3': // post
    //     return $this->renderPost($node['data'], $depth);
    //   case 't1': // comments    
    //     return $this->renderComment($node['data'], $depth);
    //   case 'more': // more replies
    //     return $this->renderMore($node['data']);
    //   default:
    //     return 'Ummm...'.$node['kind'].'<br>';
    // }
  }
  
  /**
   * Render a Reddit "more" node.
   * @param  object  $more  The object to render
   * @param  integer $depth Nesting depth
   * @return [type]         [description]
   */
  // function renderMore($more, $depth = 0) {
  //   $out = "<div data-type=\"autofill\" data-autofill=\"reddit comments " . $this->post_id . " {$more['id']}\">more</div>";
  //   return $out;
  // }
  
  /**
   * Render a Reddit "listing" node
   * @param  object  $node  The node to render
   * @param  integer $depth Nesting depth
   * @return string         Rendered output
   */
  // function renderListing($node, $depth) {
  //   $out = '';
  //   foreach ($node['children'] as $node) {
  //       $out .= self::renderReddit($node, $depth+1);
  //   }
  //   return $out;
  // }
  
  /**
   * Render a Reddit "post" node
   * @param  object  $node  The object to render
   * @param  integer $depth Nesting depth
   * @return string         Rendered output
   */
  // function renderPost($node, $depth = 0) {
  //   $tooltip = "Author: {$node['author']}\n" . 
  //              'Click comment title for command';
    
  //   $out = '<div class="reddit-post ' . ($node['ups'] > $node['downs'] ? 'upvoted' : 'downvoted') . '">';
  //   $out .= '<span class="reddit-score">';
  //   $out .= "<span title=\"{$node['ups']} up / {$node['downs']} down\">{$node['score']}</span>|";
  //   $out .= "<span data-type=\"autofill\" data-autofill=\"go {$node['url']}\" class=\"grey_text autofill\" title=\"Open post link\">L</span>|";
  //   $out .= "<span data-type=\"autofill\" data-autofill=\"go http://reddit.com{$node['permalink']}\" class=\"grey_text autofill\" title=\"{$node['num_comments']} comments.\">C({$node['num_comments']})</span> ";
  //   $out .= '</span>';
  //   $out .= $node['over_18'] ? '<span class="reddit-nsfw" title="NSFW!">:O</span> ' : ' ';
  //   $out .= "<span data-type=\"autofill\" data-autofill=\"reddit -c {$node['id']}\" title=\"$tooltip\">{$node['title']}</span> ";
    
  //   if (!empty($node['selftext']) && $this->show_selftext) {
  //     $out .= '<br>' . nl2br($node['selftext']);
  //   }
    
  //   $out .= '</div>';
  //   return $out;
  // }
  
  /**
   * Render a Reddit "comment" node
   * @param  object  $node  The object to render
   * @param  integer $depth Nesting depth
   * @return string         Rendered output
   */
  // function renderComment($node, $depth = 0) {
  //   $out = '<div style="margin-left:' . ($depth * $this->indent) . 'px">';
  //   $out .= "<span title=\"{$node['ups']} up / {$node['downs']} down\">";
  //   $out .= '['.($node['ups']-$node['downs']).'] ';
  //   $out .= '</span> ';
  //   $out .= "<span data-type=\"autofill\" data-autofill=\"reddit comments {$this->post_id} {$node['id']}\">&para;</span> ";
  //   $out .= $node['body'];
    
  //   if (isset($node['replies']['data']['children'])) {
  //       $out .= $this->renderReddit($node['replies'], $depth+1);
  //   }
    
  //   $out .= '</div>';
  //   return $out;
  // }
  
  /**
   * Render a Reddit "subreddit" node
   * @param  object  $node  The object to render
   * @param  integer $depth Nesting depth
   * @return string         Rendered output
   */
  // function renderSubreddit($node, $depth = 0) {
  //   $out = '';
  //   $out .= '<div style="margin-left:'.($depth * $this->indent).'px">';
  //   $out .= "<span data-type=\"autofill\" data-autofill=\"reddit {$node['display_name']}\">{$node['display_name']}</span> ";
  //   $out .= '</div>';
  //   return $out;
  // }
}
