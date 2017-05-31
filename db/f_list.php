<?php
ob_start();
header('Content-Type: text/html;charset=utf-8');
/*
	列表出所文件列表，包括未上传完成的，以JSON方式返回给客户端JS。
*/
require('../biz.redis/RedisTool.php');
require('../biz.redis/FileRedis.php');
require('../biz.redis/tasks.php');
require('../biz/PathTool.php');
require('../biz.model/xdb_files.php');

$uid = $_GET["uid"];
$cbk = $_GET["callback"];

if( strlen($uid) > 0)
{
	$r = RedisTool::con();
	$t = new tasks($r);
	$t->uid = $uid;
	$json = $t->toJson();
	$r->close();
	if( !empty($json) )
	{
		//echo $json;
		$json = urlencode($json);
		$json = str_replace("+","%20",$json);
		echo "$cbk({\"value\":\"$json\"})";
		return;
	}
}
echo $cbk . "({\"value\":null})";
header('Content-Length: ' . ob_get_length());
?>