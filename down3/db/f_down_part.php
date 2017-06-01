<?php
ob_start();
/*
	文件块下载
	更新记录：
		2017-06-01 创建
*/
require('../../biz.redis/RedisTool.php');
require('../../biz/PathTool.php');
require('../biz.redis/FileRedis.php');
require('../../biz/HttpHeader.php');

$head = new HttpHeader();
$lenSvr			= $head->param("f-lenSvr");
$nameLoc 		= $head->param("f-nameLoc");
$sizeLoc 		= $head->param("f-sizeLoc");
$blockPath		= $head->param("f-blockPath");
$blockIndex		= $head->param("f-blockIndex");
$blockOffset	= $head->param("f-blockOffset");
$blockSize		= $head->param("f-blockSize");
$rangeSize		= $head->param("f-rangeSize");
$lenLoc 		= $head->param("f-lenLoc");
$signSvr 		= $head->param("f-signSvr");
$percent 		= $head->param("f-percent");
$fd_signSvr		= $head->param("fd-signSvr");
$fd_lenLoc		= $head->param("fd-lenLoc");
$fd_sizeLoc		= $head->param("fd-sizeLoc");
$fd_percent		= $head->param("fd-percent");
if( !is_null($fd_sizeLoc)) $sizeLoc = $fd_sizeLoc;
if( !is_null($fd_signSvr)) $signSvr = $fd_signSvr;
if( !is_null($fd_sizeLoc)) $sizeLoc = $fd_sizeLoc;
if( !is_null($fd_percent)) $percent = $fd_percent;

$blockPath = PathTool::url_decode($blockPath);

//相关参数不能为空
if (   (strlen($lenSvr)>0)  
	&& (strlen($blockIndex)>0) 
	&& (strlen($lenLoc)>0) 
	&& (strlen($percent)>0))
{		
	$svr = RedisTool::con();
	$cache = new FileRedis($svr);
	$cache->process($signSvr,$percent,$lenLoc,$sizeLoc);
	$svr->close();
	
	$partPath = PathTool::combin($blockPath, "$blockIndex.part");
	//windows环境需要转成gbk编码
	$partPath = iconv("utf-8","gbk",$partPath);
	$downLen = intval($rangeSize) - intval($blockOffset);
	header("Content-Length: $downLen");
	header("Pragma: No-cache");
	header("Cache-Control: no-cache");
	header("Expires: 0");
	

	$fp = fopen($partPath,"rb");
	fseek($fp, $blockOffset);
	while($downLen > 0)
	{
		set_time_limit(0);
		print(fread($fp,1048576));
		flush();
		ob_flush();
		$downLen -= 1048576;
	}
	fclose($fp);
}
else
{
	header('HTTP/1.1 500 io error');
	header('Content-Length: ' . ob_get_length());	
}
?>