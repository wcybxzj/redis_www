<?php
//初始化redis对象
$redis = new Redis();

$host = "127.0.0.1";
$port = 26379;

//for 循环各个哨兵能用哪个是哪个
//连接sentinel服务 host为ip，port为端口
$redis->connect($host, $port) or die("sentinel fail");


//获取主库列表及其状态信息
//$master_info = $redis->rawCommand('SENTINEL', 'masters');

$master_name = 'mymaster';
//根据所配置的主库redis名称获取对应的信息
//master_name应该由运维告知（也可以由上一步的信息中获取）
$master_info = $redis->rawCommand('SENTINEL', 'master', $master_name);
var_dump($master_info);

//根据所配置的主库redis名称获取其对应从库列表及其信息
$slave_info = $redis->rawCommand('SENTINEL', 'slaves', $master_name);
var_dump($slave_info);



//主从都有了 不往下写了麻烦


////获取特定名称的redis主库地址
//$result = $redis->rawCommand('SENTINEL', 'get-master-addr-by-name', $master_name)
//
////这个方法可以将以上sentinel返回的信息解析为数组
//function parseArrayResult(array $data)
//{
//    $result = array();
//    $count = count($data);
//    for ($i = 0; $i < $count;) {
//        $record = $data[$i];
//        if (is_array($record)) {
//            $result[] = parseArrayResult($record);
//            $i++;
//        } else {
//            $result[$record] = $data[$i + 1];
//            $i += 2;
//        }
//    }
//    return $result;
//}


?>
