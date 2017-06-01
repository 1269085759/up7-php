<?php
/**
 * 下载列表加载页面，从up7_files中加载已上传完的文件
 */
header('Content-Type: text/html;charset=utf-8');
require('../../biz/PathTool.php');
require('../../biz.database/DbHelper.php');
require('../biz/CompleteReader.php');
require('../biz.model/DnFileInf.php');

$uid = $_GET["uid"];
$cbk = $_GET["callback"];//jsonp

if ( strlen($uid) > 0)
{
	$cb = new CompleteReader();
	$json = $cb->all($uid);		
	if( !empty($json) )
	{
		$json = PathTool::url_encode($json);		
		echo "$cbk({\"value\":\"$json\"})";
		return;
	}
}
echo $cbk . "({\"value\":null})";
?>