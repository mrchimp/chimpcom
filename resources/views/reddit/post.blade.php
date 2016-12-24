<div class="reddit-post {{ ($node['ups'] > $node['downs'] ? 'upvoted' : 'downvoted') }}">
  <span class="reddit-score">
    <span title="{{ $node['ups'] }} up / {{ $node['downs'] }} down"></span>
  </span>
  {{ $node['score'] }}|<a href="{{ $node['url'] }}" target="_blank" data-type="autofill" data-autofill="go {{ $node['url'] }}" class="grey_text autofill" title="Open post link">L</a>|<?php // any spaces here cause space on the front end!
  ?><span data-type="autofill" data-autofill="go http://reddit.com{{ $node['permalink'] }}" class="grey_text autofill" title="{{ $node['num_comments'] }} comments.">C({{ $node['num_comments'] }})</span>|<?php
  echo ($node['over_18'] ? '<span class="reddit-nsfw" title="NSFW!">:O</span>|' : '') ?><?php
  ?><span class="reddit-subreddit grey_text">/r/{{ $node['subreddit'] }}</span>|<?php
  ?><span data-type="autofill" data-autofill="reddit -c {{ $node['id'] }}" title="Author: {{ $node['author'] . "\n" }}Click comment title for command">{{ $node['title'] }}</span>
  @if(!empty($node['selftext']) && $show_selftext)
    <br>{{ nl2br($node['selftext']) }}
  @endif
</div>
