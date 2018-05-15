<?php
define('LOCK_TIMEOUT',10);//锁超时时间
define('KEY','lock_key_1');

//0 is success, 1 is fail
function get_lock($redis,$key)
{
	$val = sprintf("%u+%u+1",time(), LOCK_TIMEOUT);
	//return $redis->setNx($key,$val);
	return $redis->set($key, $val, Array('nx', 'ex'=>100));
}

function acquire_lock($redis,$key)
{
	$val_arr=array();
	for ($i = 0; $i < 3; $i++) {
		if(get_lock($redis,$key)){
			echo "get lock";
			return 1;
		}else{
			echo "not get lock, sleep(1)".PHP_EOL;
			//判断超时,怕异常终止造成锁申请但是没有释放
			$val = $redis->get($key);//TODO 可能引起错误
			$val_arr = explode("+",$val);
			$time = $val_arr[0];
			$time_out = $val_arr[1];
			$elapse = time()-$time;
			echo "elapse:".$elapse.PHP_EOL;
			if (time()-$time>$time_out) {
				$ret = $redis->delete($key);
				if (!$ret) {
					echo "delete error";
					exit(1);
				}
			}else{
				sleep(1);
			}
		}
	}
	return 0;
}

function release_lock($redis,$key)
{
	$redis->delete($key);
}

function update_lock()
{

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
