<?php
header('Content-Type: text/html;charset=utf-8');
require('../../biz/PathTool.php');
require('../../biz.redis/RedisTool.php');
require('../biz.model/DnFileInf.php');
require('../biz.redis/FileRedis.php');
require('../biz.redis/tasks.php');
require('../biz.redis/KeyMaker.php');
/*
	更新记录：
		2015-05-13 创建
		2016-07-29 更新
		2017-06-01 完善
*/
$uid 	 = $_GET["uid"];
$cbk	 = $_GET["callback"];
$signSvr = $_GET["signSvr"];
$nameLoc = $_GET["nameLoc"];
$pathLoc = $_GET["pathLoc"];
$sizeSvr = $_GET["sizeSvr"];
$nameLoc = PathTool::url_decode($nameLoc);
$pathLoc = PathTool::url_decode($pathLoc);
$sizeSvr = PathTool::url_decode($sizeSvr);

if ( $uid==""
	|| $signSvr==""
	|| $pathLoc==""
	)
{
	echo 0;
	return;
}

$f = new DnFileInf();
$f->nameLoc = $nameLoc;
$f->pathLoc = $pathLoc;
$f->sizeSvr = $sizeSvr;
$f->folder = true;

$j = RedisTool::con();
$svr = new tasks($uid, $j);
$svr->add($f);
$j->close();

echo "$cbk(0)";
?>