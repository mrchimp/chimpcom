<div class="reddit-post <?php echo ($node['ups'] > $node['downs'] ? 'upvoted' : 'downvoted') ?>">
  <span class="reddit-score">
    <span title="<?php echo $node['ups'] ?> up / <?php echo $node['downs'] ?> down"></span>
  </span>
  <?php echo $node['score'] ?>|<a href="<?php echo $node['url'] ?>" target="_blank" data-type="autofill" data-autofill="go <?php echo $node['url'] ?>" class="grey_text autofill" title="Open post link">L</a>|<?php // any spaces here cause space on the front end!
  ?><span data-type="autofill" data-autofill="go http://reddit.com<?php echo $node['permalink'] ?>" class="grey_text autofill" title="<?php echo $node['num_comments'] ?> comments.">C(<?php echo $node['num_comments'] ?>)</span>|<?php
  echo ($node['over_18'] ? '<span class="reddit-nsfw" title="NSFW!">:O</span>|' : '') ?><?php
  ?><span class="reddit-subreddit grey_text">/r/<?php echo $node['subreddit'] ?></span>|<?php
  ?><span data-type="autofill" data-autofill="reddit -c <?php echo $node['id'] ?>" title="Author: <?php echo $node['author'] . "\n" ?>Click comment title for command"><?php echo $node['title'] ?></span> 
  <?php if (!empty($node['selftext']) && $show_selftext) : ?>
    <br><?php echo nl2br($node['selftext']) ?>
  <?php endif; ?>
</div>
