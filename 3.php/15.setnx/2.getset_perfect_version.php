<?php
//getset()+锁释放细化版(),来实现setnx的完美版本
define('EX',1000);
define('KEY',"lock_key");
define('VAL_FORMAT',"%d-%d-%s");
define("TIME_OUT",5);

define("STATE_OK",0);
define("STATE_FORMAT_ERROR",1);
define("STATE_TIMEOUT",2);

function acquire_lock($redis, $key)
{
	$value = create_val();
	$ret = $redis->set($key, $value, Array('nx', 'ex'=>EX));
	if ($ret) {
		return $value;
	}else{
		return FALSE;
	}
}

function release_lock($redis,$key,$val)
{
	//只有是自己设置的锁才能正常释放
	$ret_val = $redis->get($key);
	//echo "本进程设置的val:".$val;
	//echo "\n";
	//echo "Redis现在的val:".$ret_val;
	if (strcasecmp($val,$ret_val)==0) {
		return $redis->delete($key);
	}else{
		return FALSE;
	}
}

function check_val($val)
{
	if (!$val) {
		return FALSE;
	}
	$arr = explode("-", $val);
	if (!is_array($arr) || count($arr)!=3) {
		//echo "val format error";
		return STATE_FORMAT_ERROR;//格式错误
	}
	$max_time = $arr[0]+$arr[1];
	//echo $max_time;
	//echo "\n";
	//echo time();
	if ($max_time<time()) {
		//echo "key timeout";
		return STATE_TIMEOUT;//已经超时
	}
	return STATE_OK;
}

function try_lock($redis,$key)
{
	$ret;
	$get_val = acquire_lock($redis, $key);
	if (!$get_val) {
		$get_val = $redis->get($key);
		$ret = check_val($get_val);
		if ($ret==STATE_OK) {//key还没过期
			return FALSE;
		}
		$current_val = create_val();
		$get_val= $redis->getSet($key,$current_val);
		$ret = check_val($get_val);
		if ($ret==STATE_TIMEOUT) {
			return $current_val;
		}else{
			FALSE;
		}
	}
	return $get_val;
}

function create_random()
{
	$tmp = range(100,120);
	$tmp2 = array_rand($tmp,1);
	return getmypid()."+".microtime(1)."+".$tmp2;
}

function create_val()
{
	return sprintf(VAL_FORMAT, time(), TIME_OUT, create_random());
}
?>
