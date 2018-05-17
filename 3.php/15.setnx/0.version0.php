<?php
define('KEY','lock_key_0');
$redis = new Redis();
$redis->connect('127.0.0.1', 6379) or die('redis not connect');


function acquire_lock($redis, $key)
{
	return $redis->set($key, 'value', Array('nx', 'ex'=>10));
}

function release_lock($redis,$key)
{
	$redis->delete($key);
}

$redis = new Redis();
 $redis->connect('127.0.0.1', 6379) or die('redis not connect');
$get = acquire_lock($redis, KEY);
if (!$get) {
	echo "not get lock".PHP_EOL;
	exit(1);
}else{
	echo "get lock".PHP_EOL;
	for ($i = 0; $i < 8; $i++) {
		echo "work on redis".PHP_EOL;
		sleep(1);
	}
}

release_lock($redis, KEY);
unset($redis);

?>
