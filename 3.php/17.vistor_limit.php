<?php
//60秒只能发5次短信

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);


//1分钟最多10次请求
define("TIME",60);
define('NUM',10);

$uid=123;
$key = "limit:".$uid;

$len = $redis->lLen($key);
if ($len<NUM) {
	$redis->lPush($key,time());
}else{
	$time = $redis->lIndex($key,-1);
	if (time()-$time < TIME) {
		echo "访问受限";
		die;
	}else{
		$redis->lPush($key,time());
		$redis->lTrim($key,0,9);
	}
}

echo "访问ok";
