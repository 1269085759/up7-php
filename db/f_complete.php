<?php
ob_start();
header('Content-Type: text/html;charset=utf-8');
/*
	此文件只负责将数据表中文件上传进度更新为100%
		向数据库添加新记录在 ajax_create_fid.php 文件中处理
	如果服务器不存在此文件，则添加一条记录，百分比为100%
	如果服务器已存在相同文件，则将文件上传百分比更新为100%
*/
require('biz.database/DbHelper.php');
require('biz.database/DBFile.php');
require('biz.redis/RedisTool.php');
require('biz.redis/FileRedis.php');

$uid 		= $_GET["uid"];
$idSign 	= $_GET["idSign"];
$merge 		= $_GET["merge"];
$cbk 		= $_GET["callback"];
$ret 		= 0;

if ( 	strlen($uid) > 0
	&& 	strlen($idSign) > 0 )
{
	$r = RedisTool::con();
	$cache = new FileRedis($r);
	$f = $cache->read($idSign);
	
	//合并文件	
	$r->del($idSign);//删除文件缓存
	
	//从任务列表（未完成）中删除
	$t = new tasks($r);
	$t->uid = $uid;
	$t->del($idSign);
	$r->close();
	
	$db = new DBFile();
	$db->addComplete($f);
	$ret = 1;
}

//返回查询结果
echo "$cbk( $ret )";
header('Content-Length: ' . ob_get_length());
?>