<?php 
require('../../biz/PathTool.php');
require('../../biz.redis/RedisTool.php');
require('../biz.redis/FileRedis.php');
require('../biz.redis/tasks.php');
require('../biz.redis/KeyMaker.php');
require('../biz.model/DnFileInf.php');

$uid 		= $_GET["uid"];
$signSvr 	= $_GET["signSvr"];
$nameLoc 	= $_GET["nameLoc"];
$pathLoc 	= $_GET["pathLoc"];
$pathLoc 	= $_GET["pathSvr"];
$pathSvr 	= $_GET["fileUrl"];
$lenSvr 	= $_GET["lenSvr"];
$sizeSvr 	= $_GET["sizeSvr"];
$cbk 		= $_GET["callback"];
$pathLoc	= PathTool::url_decode($pathLoc);
$nameLoc	= PathTool::url_decode($nameLoc);

if (  strlen($uid) < 1
	||empty($pathLoc)
	||empty($pathSvr)
	||empty($lenSvr))
{
	echo cbk . "({\"value\":null})";
	die();
}

$inf = new DnFileInf();
$inf->uid = intval($uid);
$inf->nameLoc = $nameLoc;
$inf->pathLoc = $pathLoc;
$inf->fileUrl = $pathSvr;
$inf->lenSvr  = $lenSvr;
$inf->sizeSvr = $sizeSvr;

//添加到缓存
$j = RedisTool::con();
$svr = new tasks($uid,$j);
$svr->add($inf);
$j->close();

//防止jsonencode将汉字转换为unicode
$inf->nameLoc = PathTool::urlencode_safe($inf->nameLoc);
$inf->pathLoc = PathTool::urlencode_safe($inf->pathLoc);
$json = json_encode($inf);
$json = urldecode($json);//还原汉字
$json = urlencode($json);
$json = "$cbk({\"value\":\"$json\"})";//返回jsonp格式数据。
echo $json;
?>