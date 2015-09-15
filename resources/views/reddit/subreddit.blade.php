<?php 
foreach ($node['children'] as $subnode) {
  echo view('reddit.reddit', [
    'node' => $subnode,
    'depth' => $depth,
    'indent' => $indent
  ])->render();
}
