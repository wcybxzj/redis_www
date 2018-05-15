<?php
$redis = new Redis();
$redis->connect('127.0.0.1', 6379) or die('redis not connect');


$redis->set("ybx",123);
echo $redis->delete("ybx");
echo $redis->delete("ybx");

