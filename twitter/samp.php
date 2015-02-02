<?php

require_once('TwitterAPIExchange.php');

include('config.php');
include("proccommand.php");

/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'oauth_access_token' => OAUTH_ACCESS_TOKEN,
    'oauth_access_token_secret' => OAUTH_ACCESS_TOKEN_SECRET,
    'consumer_key' => CONSUMER_KEY,
    'consumer_secret' => CONSUMER_SECRET
);

ini_set('auto_detect_line_endings',true);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

$twitter = new TwitterAPIExchange($settings);
	
while(true) {
/** Note: Set the GET field BEFORE calling buildOauth(); **/
	$url = 'https://api.twitter.com/1.1/application/rate_limit_status.json';
	$getfield = '?resources=search';
	$requestMethod = 'GET';
	
	$response = json_decode($twitter->setGetfield($getfield)
				 ->buildOauth($url, $requestMethod)
				 ->performRequest(),true);
	print_r($response);

	
for ($ii=0;$ii<25;$ii++) {
	
	
	if ($ii % 5 == 0) {
	/** Note: Set the GET field BEFORE calling buildOauth(); **/
	$url = 'https://api.twitter.com/1.1/search/tweets.json';
	$getfield = '?q='.urlencode('@').'twiplayspokemon';
	$requestMethod = 'GET';

	if (strlen($since_id)) $getfield.="&since_id=$since_id&result_type=recent";

	$response = json_decode($twitter->setGetfield($getfield)
				 ->buildOauth($url, $requestMethod)
				 ->performRequest(),true);

				 
	$tweets = $response['statuses'];

	if (strlen($since_id)) {
	foreach($tweets as $tweet) {
		proctweet($tweet);
	}
	}
	if (strlen($response['search_metadata']['max_id_str'])) {
		$since_id = $response['search_metadata']['max_id_str'];
		echo "--$since_id\n";
	}
	else {
		echo "No response?\n";
		print_r($response);
	}
	}
	proccommand(null);
		sleep(1);
		#usleep(2500000);
	}
}
  
  
  
function buffer_add($s,$force = false) {
    global $fbuff,$buffbytes,$buff,$lastwrite;
    if (!$fbuff) {
      $fbuff = fopen("tmp/.buff.tmp","ab");
      @mkdir("tmp");
      }
    
    $buff .= $s;
    $buffbytes+=strlen($s);
    
    if ((time()-$lastwrite)>1 or $force) {
      $lastwrite = time();
      if (strlen($buff)) fwrite($fbuff,$buff);
      $buff = "";
      }
}
  

function nullfilter($arr) {
  
  foreach($arr as $key=>$value) {
    if (strpos($key,"profile")!==FALSE && $key != "profile_image_url") unset($arr[$key]);
    elseif (is_array($value)) {
      $arr[$key]=nullfilter($value);
      if (!count($arr[$key])) unset($arr[$key]);
      if ($key === "place" || $key === "indices") unset($arr[$key]);
      }
    elseif (isset($arr["{$key}_str"])) unset($arr[$key]);
    elseif ($value === null || $value === false || $value === 0 || $value === "") unset($arr[$key]);
    elseif ($key === "created_at" && strlen($value)>12) {
      $arr[$key]=strtotime($value);
      }
    elseif ($key === "source") {
      $arr[$key]=strip_tags($value);
      }
    }
  
  return $arr;
}
  
  
function proctweet($arr) {
  
	$arr = nullfilter($arr);
	
	$data['tweetid']=$arr['id_str'];
	$data['userid']=$arr['user']['id_str'];
	$data['username']=$arr['user']['screen_name'];
	$data['data']=json_encode($str);
	$data['text']=$arr['text'];
	$data['time']=$arr['created_at'];
	
	$data['command'] = proccommand($data);
}
  
  
  
  
  ?>