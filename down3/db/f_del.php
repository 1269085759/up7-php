<?php 
require('../../biz/PathTool.php');
require('../../biz.redis/RedisTool.php');
require('../biz.redis/tasks.php');

$fid = $_GET["signSvr"];
$uid = $_GET["uid"];
$cbk = $_GET["callback"];//jsonp

if ( strlen($uid)<1 ||	empty($fid)	)
{
	echo $cbk . "({\"value\":null})";
	die();
}
$j = RedisTool::con();
$svr = new tasks($uid, $j);
$svr->del($fid);
$j->close();
echo $cbk . "({\"value\":1})";
?>