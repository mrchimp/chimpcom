<?php 
$permalink = 'https://www.reddit.com/r/' . $node['subreddit'] . '/comments/' . $node['link_id'].'/'.$node['id'];
 ?>
<div style="margin-left:<?php echo $depth * $indent; ?>px">
  <span class="inverted" title="<?php echo $node['ups'] ?> up / <?php echo $node['downs'] ?> down"><?php echo ($node['ups'] - $node['downs']) ?></span>|<?php
  ?><a href="<?php echo $permalink; ?>" data-type="autofill" data-autofill="reddit comments <?php echo $post_id?> <?php echo $node['id'] ?>">&para;</a>
  <?php echo $node['body']; ?>
  <?php if (isset($node['replies']['data']['children'])) : ?>
    <?php echo view('reddit.reddit', [
      'node' => $node['replies'],
      'depth' => $depth+1
    ])->render(); ?>
  <?php endif; ?>
</div>
