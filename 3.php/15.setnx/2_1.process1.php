<?php
include_once "./2.getset_perfect_version.php";

if ($argc!=2) {
	printf("./a.out second_num");
	die;
}
$second_num = $argv[1];
echo "获取锁后sleep时间是".$second_num."秒";
echo "当前进程是:".getmypid();
echo "\n";
$redis = new Redis();
$redis->connect('127.0.0.1', 6379) or die('redis not connect');

$ret_val = try_lock($redis,KEY);
if ($ret_val) {
	echo "成功获取锁";
}else{
	echo "未能获取锁";
	unset($redis);
	die;
}

for ($i = 0; $i < $second_num; $i++) {
	echo "work on redis".PHP_EOL;
	sleep(1);
}

$ret = release_lock($redis, KEY, $ret_val);
if ($ret) {
	echo "成功获释放锁";
}else{
	echo "未能获释放锁";
	unset($redis);
	die;
}
?>
