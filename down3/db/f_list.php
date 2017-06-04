<?php
/**
 * 从缓存(redis)中加载未下载完毕的任务列表
 */
require('../../biz.database/DbHelper.php');
require('../../biz/PathTool.php');
require('../../biz.redis/RedisTool.php');
require('../biz.redis/FileRedis.php');
require('../biz.redis/tasks.php');
require('../biz.redis/KeyMaker.php');
require('../biz.model/DnFileInf.php');
require('../../biz.model/xdb_files.php');

$uid = $_GET["uid"];
$cbk = $_GET["callback"];//jsonp

if ( strlen($uid)>0 )
{
	$j = RedisTool::con();
	$svr = new tasks($uid, $j);
	$json = $svr->toJson();
	$j->close();	
	
	if( !empty($json) )
	{
		$json = PathTool::url_encode($json);
		echo "$cbk({\"value\":\"$json\"})";
		return;
	}
}

echo $cbk . "({\"value\":null})";
?>