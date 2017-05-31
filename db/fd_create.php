<?php
ob_start();
header('Content-Type: text/html;charset=utf-8');
/*
	更新记录：
		2014-07-23 创建
		2014-08-05 修复BUG，上传文件夹如果没有子文件夹时报错的问题。
		2014-09-12 完成逻辑。
		2014-09-15 修复设置子文件，子文件夹层级结构错误的问题。
		2016-04-13 以md5模式上传文件夹
		2016-05-29 修复添加文件夹数据错误的问题。
		2017-05-31 优化逻辑，取消POST整个文件夹数据，而是化整为零传小数据。
*/
require('../biz/PathTool.php');
require('../biz/PathBuilder.php');
require('../biz/PathGuidBuilder.php');
require('../biz.model/xdb_files.php');
require('../biz.redis/RedisTool.php');
require('../biz.redis/FileRedis.php');
require('../biz.redis/tasks.php');

$idSign  = $_GET["idSign"];
$pathLoc = $_GET["pathLoc"];
$sizeLoc = $_GET["sizeLoc"];
$lenLoc  = $_GET["lenLoc"];
$uid 	 = $_GET["uid"];
$fCount  = $_GET["filesCount"];
$cbk	 = $_GET["callback"];
$pathLoc = PathTool::url_decode($pathLoc);

$fileSvr = new xdb_files();
$fileSvr->nameLoc = PathTool::getName($pathLoc);
$fileSvr->nameSvr = $fileSvr->nameLoc;
$fileSvr->idSign  = $idSign;
$fileSvr->pathLoc = $pathLoc;
$fileSvr->sizeLoc = $sizeLoc;
$fileSvr->lenLoc = $lenLoc;
$fileSvr->filesCount = $fCount;
$fileSvr->folder = true;
//生成路径
$pb = new PathGuidBuilder();
$fileSvr->pathSvr = $pb->genFolder($uid, $fileSvr->nameLoc);

//添加到缓存
$con = RedisTool::con();
$svr = new tasks($con);
$svr->uid = $uid;
$svr->add($fileSvr);
$con->close();

//将数组转换为JSON
$fileSvr->nameLoc = PathTool::url_encode($fileSvr->nameLoc);
$fileSvr->nameSvr = PathTool::url_encode($fileSvr->nameSvr);
$fileSvr->pathLoc = PathTool::url_encode($fileSvr->pathLoc);
$fileSvr->pathSvr = PathTool::url_encode($fileSvr->pathSvr);
$json = json_encode($fileSvr);
$json = PathTool::url_decode($json);//还原汉字
$json = PathTool::url_encode($json);

echo "({\"ret\":\"$json\"})";
header('Content-Length: ' . ob_get_length());
?>