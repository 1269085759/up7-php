<?php 
require('../../biz.redis/RedisTool.php');
require('../biz.redis/FileRedis.php');

$uid 	= $_GET["uid"];
$fid 	= $_GET["signSvr"];
$lenLoc	= $_GET["lenLoc"];
$sizeLoc= $_GET["sizeLoc"];
$per	= $_GET["perLoc"];
$cbk 	= $_GET["callback"];//jsonp

if (   $uid == ""
	|| $fid == ""
	|| $cbk == ""
	|| $lenLoc== "" )
{
	echo $cbk . "({\"value\":0})";
	return;
}

$j = RedisTool::con();
$fr = new FileRedis($j);
$fr->process($fid, $per, $lenLoc, $sizeLoc);
echo $cbk . "({\"value\":1})";
?>