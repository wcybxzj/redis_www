<?php
//function customError($errno, $errstr)
// { 
//  echo "<b>Error:</b> [$errno] $errstr<br />";
//  echo "Ending Script";
//  die();
//  }
//
//set_error_handler("customError");



$obj_cluster = new RedisCluster(NULL, Array('127.0.0.1:6379', '127.0.0.1:6380', '127.0.0.1:6381'));
$key = "key23269";

try
{
	echo $obj_cluster->get($key);
}
catch(Exception $e)
{
	echo 'Message: ' .$e->getMessage();
}

