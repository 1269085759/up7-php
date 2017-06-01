<?php
ob_start();
/*
	控件每次向此文件POST数据
	逻辑：
		1.更新数据库进度
		2.将文件块数据保存到服务器中。
	更新记录：
		2017-05-31 创建
		2017-06-01 增加目录信息 
*/
require('../biz.database/DbHelper.php');
require('../biz.database.folder/FilesWriter.php');
require('../biz.database.folder/FoldersWriter.php');
require('../biz.redis/RedisTool.php');
require('../biz.redis/FileRedis.php');
require('../biz.redis/tasks.php');
require('../biz.model/xdb_files.php');

$sign   = $_GET["idSign"];
$uid	= $_GET["uid"];
$cbk 	= $_GET["callback"];//jsonp
$ret 	= 0;

//参数为空
if ( strlen($sign) > 0 )
{
	$r = RedisTool::con();
	$cache = new FileRedis($r);
	$fd = $cache->read($sign);	
	
	//保存目录信息
	$fdw = new FoldersWriter($r, $fd);
	$fdw->saveAll();
	
	//将缓存数据写到数据库
	$w = new FilesWriter($r, $fd);
	$w->saveAll();
		
	//清除缓存
	$svr = new tasks($r);
	$svr->uid = $uid;
	$svr->delFd($sign);
	$r->close();
	$ret = 1;
}
echo "$cbk( $ret )";
header('Content-Length: ' . ob_get_length());
?>