<?php
ob_start();
header('Content-Type: text/html;charset=utf-8');
/*
	只文件根据UID和FID删除数据库中文件项。不真正删除数据，只将设置删除标识
	返回值：
		1 删除成功
		0 删除失败
	更新记录：
		2012-4-2 创建
		2014-09-12 完成逻辑。
*/
require('DbHelper.php');
require('DBFile.php');

$idSign = $_GET["idSign"];
$uid = $_GET["uid"];
$cbk = $_GET["callback"];
$ret = $cbk . "(0)";

if (	strlen($idSign) > 0 
	&&	strlen($uid) > 0)
{
	$r = RedisTool::con();
	$cache = new tasks($r);
	$cache->uid = $uid;
	$cache->del($idSign);
	$r->close();
	
	$db = new DBFile();
	$db->remove($idSign);
	$ret = $cbk . "(1)";
}

//返回查询结果
echo $ret;
header('Content-Length: ' . ob_get_length());
?>