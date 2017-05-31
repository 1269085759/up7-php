<?php 
ob_start();
header('Content-Type: text/html;charset=utf-8');

require('../biz.redis/RedisTool.php');
require('../biz.redis/FileRedis.php');

$id 		= $_GET["idSign"];
$perSvr 	= $_GET["perSvr"];
$lenSvr 	= $_GET["lenSvr"];

if(    strlen(id) < 1 
	|| strlen(perSvr) < 1	
	|| strlen(lenSvr) <1 )
{
	echo "param is null";		
	die();
}

$j = RedisTool::con();
$fr = new FileRedis($j);
$fr->process($id, $perSvr, $lenSvr, "0", "0");

header('Content-Length: ' . ob_get_length());
?>