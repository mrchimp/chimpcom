<?php 

switch ($node['kind']) {
  case 'Listing':
    foreach ($node['data']['children'] as $child) {
      echo view('reddit.reddit', [
        'node'   => $child,
        'depth'  => $depth + 1,
        'indent' => $indent        
      ])->render();
    }
    break;
  case 't5': // subreddit
    echo view('reddit.subreddit', [
      'node'   => $node['data'],
      'depth'  => $depth,
      'indent' => $indent
    ])->render();
    break;
  case 't3': // post
    echo view('reddit.post', [
      'node'   => $node['data'],
      'depth'  => $depth,
      'indent' => $indent
    ])->render();
    break;
  case 't1': // comments
    echo view('reddit.comment', [
      'node'   => $node['data'],
      'depth'  => $depth,
      'indent' => $indent
    ])->render();
    break;
  case 'more': // more replies
    echo view('reddit.more', [
      'node'   => $node['data'],
      'indent' => $indent
    ])->render();
    break;
  default:
    echo 'Ummm...'.$node['kind'].'.<br>';
}
