<?php 
/**
 * This is a generic class for managing 
 */


class Twitterfier{



  protected function cache_tweets($collection_id='latest_tweets', $tweetsdata=''){
    update_option($collection_id, $tweetsdata);
  } 


  protected function get_cached_tweets($collection_id='latest_tweets'){
    return get_option($collection_id,'[]');
  }

  protected function do_http_request($endpoint, $headers, $data, $method='POST'){
    $response = new stdClass(); 

    $response->data = '';
    $response->code = 0;

     // do something more generic  - here - or detect whther this is WP or Drupal and use those native first, otherwise fallback to Curl
      // $response  = drupal_http_request($endpoint, array('headers'=>$headers, 'method'=>$method, 'data'=>$data));

    if($method == 'POST'){
      $response_array = wp_remote_post($endpoint, array('headers'=>$headers, 'body'=>$data));
    } elseif ( $method =='GET'){
      $response_array = wp_remote_get($endpoint, array('headers'=>$headers, 'body'=>$data));
    }


    if(is_wp_error($response_array)){

      return false;
    }

    $response->code = $response_array['response']['code'];
    $response->data = $response_array['body'];

    return $response; 
  }



  public function init($consumer_key, $consumer_secret){
    $this->consumer_key = $consumer_key;
    $this->consumer_secret = $consumer_secret;
  }


 private function auth($options = array()){
  /**
   * hard coding the auth tokens here for now
   */
    $twitter_auth_endpoint = 'https://api.twitter.com/oauth2/token';



    $auth = 'Basic '.base64_encode(urlencode($this->consumer_key).':'.urlencode($this->consumer_secret));
    $method = 'POST';
    $data = 'grant_type=client_credentials';

    $headers = array(
      'Authorization' => $auth,
      'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
      );

    $response = $this->do_http_request($twitter_auth_endpoint, $headers, $data);

    if(!$response || !$response->data){
      return false;
    }
    $response_obj = json_decode($response->data);
    if($response_obj->token_type == 'bearer'){
      return 'Bearer '. ($response_obj->access_token);
    }
    return false;
}


/**
 * get the latest tweets! - 
 * generally this would be run from a cron
 */
public function get_tweets($options = array()){
  $defaults = array(
    'screen_name'=>'admataz',
    'count'=>20,
    'exclude_replies'=>true,
    'include_rts'=>true
    );
  $options = array_merge($defaults, $options);
  $q = http_build_query($options);
  $auth_token = $this->auth();
  if(!$auth_token){
    return array();
  }
  $headers = array(
    'Authorization' => $auth_token
    );
  $method = 'GET';
  $response  = $this->do_http_request('https://api.twitter.com/1.1/statuses/user_timeline.json?'.$q, $headers, array(), $method);
  // Caches the json in the database for retreival 
  if($response->code =='200'){
    $this->cache_tweets('latest_tweets', $response->data);
  }


}


public function output($num=5){
  $tweetsdata = json_decode($this->get_cached_tweets('latest_tweets'));
  $tweets = array();
  foreach($tweetsdata as $t){
      $t->text = $this->twitterify($t->text);
      $tweets[] = $t;
  }
  return array_slice($tweets,0,$num);
}


/**
 * from http://www.snipe.net/2009/09/php-twitter-clickable-links/
 */
private function twitterify( $ret, $hashes=TRUE, $ats=TRUE, $trim=0, $ellipsis='…' ) {
  if ( $trim ) {
    $words = explode( ' ', $ret );
    if ( count( $words ) > $trim ) {
      array_splice( $words, $trim );
      $ret = implode( ' ', $words );
      if ( is_string( $ellipsis ) ) {
        $ret .= $ellipsis;
      }
      elseif ( is_string( $ellipsis ) ) {
        $ret .= $ellipsis;
      }
    }
  }

  $ret = preg_replace_callback( '~&#x([0-9a-f]+);~i', function($m){
    return chr(hexdec($m[1]));
  }, $ret );


  $ret = preg_replace_callback( '~&#([0-9]+);~', function($m){
    return chr($m[1]);
  }, $ret );

  $ret = preg_replace_callback( "#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", array($this,"shorten_url_title1"), $ret );
  $ret = preg_replace_callback( "#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", array($this, "shorten_url_title2"), $ret );

  if ( $hashes ) {
    $ret = preg_replace_callback( "/@(\w+)/", function($m){
      return "<a href=\"http://www.twitter.com/{$m[1]}\" target=\"_blank\">@{$m[1]}</a>";
    }, $ret );
  }

  if ( $ats ) {
    $ret = preg_replace_callback( "/#(\w+)/", function($m){
      return "<a href=\"http://twitter.com/search?q={$m[1]}\" target=\"_blank\">#{$m[1]}</a>";
    }, $ret );
  }
  return $ret;
}




/**
 *
 */
private function shorten_url_title1( $match ) {
  if ( strlen( $match[2] ) > 16 ) {
    $title = substr( $match[2], 0, 20 ).'…';
  }else {
    $title = $match[2];
  }
  return "{$match[1]}<a href=\"{$match[2]}\" target=\"_blank\">".$title."</a>";
}





/**
 *
 */
private function shorten_url_title2( $match ) {
  if ( strlen( $match[2] ) > 16 ) {
    $title = substr( $match[2], 0, 20 ).'…';
  }else {
    $title = $match[2];
  }

  return "{$match[1]}<a href=\"{$match[2]}\" target=\"_blank\">".$title."</a>";
}
}
