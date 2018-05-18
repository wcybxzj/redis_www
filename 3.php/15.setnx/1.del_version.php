<?php
//del版的setnx上锁列子
define('EX',1000);
define('KEY',"lock_key");
define('VAL_FORMAT',"%d-%d");
define("TIME_OUT",5);

function acquire_lock($redis, $key)
{
	$value = sprintf(VAL_FORMAT, time(), TIME_OUT);
	return $redis->set($key, $value, Array('nx', 'ex'=>EX));
}

function release_lock($redis,$key)
{
	return $redis->delete($key);
}

function try_lock($redis,$key)
{
	$ret;
	$get = acquire_lock($redis, $key);
	if (!$get) {
		$val = $redis->get($key);
		if (!$val) {
			return FALSE;
		}
		$arr = explode("-", $val);
		if (!is_array($arr) || count($arr)!=2) {
			return FALSE;
		}
		$max_time = $arr[0]+$arr[1];
		//echo $max_time;
		//echo "\n";
		//echo time();
		if (!($max_time<time())) {//key没有超时所以不能获取key_lock
			return FALSE;
		}
		$ret = $redis->del($key);
		if (!$ret) {
			return FALSE;
		}
		$ret = acquire_lock($redis, $key);
		if (!$ret) {
			return FALSE;
		}
	}else{
		echo "get lock".PHP_EOL;
		for ($i = 0; $i < 20; $i++) {
			echo "work on redis".PHP_EOL;
			sleep(1);
		}
	}
	return TRUE;
}

$redis = new Redis();
$redis->connect('127.0.0.1', 6379) or die('redis not connect');

$ret = try_lock($redis,KEY);
if ($ret) {
	echo "成功获取锁";
}else{
	echo "未能获取锁";
	die;
}

$ret = release_lock($redis, KEY);
if ($ret) {
	echo "成功获释放锁";
}else{
	echo "未能获释放锁";
	die;
}
unset($redis);

?>
