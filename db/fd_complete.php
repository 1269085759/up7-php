<?php
ob_start();
/*
	控件每次向此文件POST数据
	逻辑：
		1.更新数据库进度
		2.将文件块数据保存到服务器中。
	更新记录：
		2014-04-09 增加文件块验证功能。
		2014-09-12 完成逻辑。
		2014-09-15 修复返回JSONP数据格式错误的问题。
		2016-05-31 优化调用，DBFolder::Complete会自动更新文件表信息，所以在此页面不需要再单独调用DBFile::fd_complete
*/
require('../biz.database/DbHelper.php');
require('../biz.database.folder/FilesWriter.php');
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
		
	//清除缓存
	$svr = new tasks($r);
	$svr->uid = $uid;
	$svr->delFd($sign);
	$r->close();
	
	//将缓存数据写到数据库
	$w = new FilesWriter($r, $fd);
	$ret = 1;
}
echo "$cbk( $ret )";
header('Content-Length: ' . ob_get_length());
?>