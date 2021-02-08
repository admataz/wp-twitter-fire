# wp-twitter-fire

WordPress plugin to embed Twitter feeds on a page. This communicates with the Twitter API from the server to retrieve and cache the 
Tweets locally for custom display in the WordPress site public interface. This provides better customisation capabilities to the
standard embeddable client-side timelines available from Twitter - but comes with less functionality (no retweets, etc)

Requires a Twitter app and API keys set up in  https://developer.twitter.com/en/apps/

## Installing
1. Activate the plugin in the WP admin interface
2. Set the configuration options at `/wp-admin/options-general.php?page=twitter-fire`


## Using
Once set up, the plugin uses WordPress `cron` hooks to check for new tweets every 5 minutes,  downloads and saves the payload from the Twitter API 
to the database using WordPress `update_option`. 

The Twitterfier plugin uses `apply_filters` to output a PHP `array` for consumption in templates, like in the following example: 

```php
<?php
  $tweets = apply_filters('twitterfire_get_tweets', '', 5);
  foreach($tweets as $tweet) {
    $created_at = date("H:i, l j M",strtotime($tweet->created_at));
    echo "<li class='tweet' title='$created_at'><div id='tweetArrow'></div><p class='tweetText'>" . $tweet->text . "<small>$created_at</small></p></li>";
  }
?>
```

For more information on the available properties for the `tweet` object, refer to the Twitter API documentation for [Tweet Objects](https://developer.twitter.com/en/docs/tweets/data-dictionary/overview/tweet-object) and [Statuses/ User Timelines](https://developer.twitter.com/en/docs/tweets/timelines/api-reference/get-statuses-user_timeline.html)



