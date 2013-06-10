<pre>
<a href="?">re-check unfollowers</a> | <a href="?recreate=1">re-create followers file with current followers</a><br/>

<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

$userName = 'YOUR_TWITTER_USERNAME';
$savedFollowersFile = 'followers.txt';
/*
you must create an empty file in this directory with the same name as $savedFollowersFile (to prevent file creation and writing permission issues)
*/

$followers = json_decode(file_get_contents('https://api.twitter.com/1/followers/ids.json?cursor=-1&screen_name=' . $userName));
$followers = $followers->ids;

if($_GET['recreate'] == 1){
	$defaultFollowersFile = fopen($savedFollowersFile, "r+");
	ftruncate($defaultFollowersFile, 0);
	fclose($defaultFollowersFile);
}

$savedFollowers = file($savedFollowersFile);

if(sizeof($savedFollowers) == 0){
	$defaultFollowersFile = fopen($savedFollowersFile, "a");
	foreach ($followers as $f) {
		fprintf($defaultFollowersFile, $f . "\n");
	}
	print 'default followers file is created.<br/>';
	fclose($defaultFollowersFile);
}

foreach ($savedFollowers as $key => $cf){
	$savedFollowers[$key] = intval($cf);
}

$fx = array_diff($savedFollowers, $followers);

if(sizeof($fx) == 0){
	print 'no unfollow since last save.';
}else{
	print sizeof($fx) . ' unfollower(s):<br/>';
	foreach ($fx as $f){
		$fw = json_decode(file_get_contents('https://api.twitter.com/1/users/show.json?user_id='.(string)$f.'&include_entities=false'));
		print '<a target="_blank" href="http://www.twitter.com/'. $fw->screen_name . '">' . mb_convert_encoding($fw->name,'HTML-ENTITIES','utf-8'). '</a><br/>';
	}
}
?>

</pre>