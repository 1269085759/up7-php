<?php 
ob_start();
header('Content-Type: text/html;charset=utf-8');

require('../biz.redis/RedisTool.php');
require('../biz.redis/FileRedis.php');

$uid 		= $_GET["uid"];
$sign 		= $_GET["sign"];
$idSign 	= $_GET["idSign"];
$perSvr 	= $_GET["perSvr"];
$lenSvr 	= $_GET["lenSvr"];
$lenLoc 	= $_GET["lenLoc"];
$blockSize 	= $_GET["blockSize"];

if(    strlen($uid) < 1 
	|| strlen($lenLoc) < 1
	|| strlen($idSign) <1 )
{
	echo "param is null";		
	die();
}

//更新redis进度
$r = RedisTool::con();
$fr = new FileRedis($r);
$fr->process($idSign,$perSvr,$lenSvr,"0",blockSize);

header('Content-Length: ' . ob_get_length());
?>