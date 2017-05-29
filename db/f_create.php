<?php
ob_start();
header('Content-type: text/html;charset=utf-8');
/*
	此文件主要功能如下：
		1.在数据库中添加新记录
		2.返回新加记录信息。JSON格式
		3.创建上传目录
	此文件主要在数据库中添加新的记录并返回文件信息
		如果存在则在数据库中添加一条相同记录。返回添加的信息
		如果不存在，则向数据库中添加一条记录。并返回此记录ID
	控件每次计算完文件MD5时都将向信息上传到此文件中
	@更新记录：
		2014-08-12 完成逻辑。
		2017-05-29 增加块路径生成逻辑
*/
require('DbHelper.php');
require('DBFile.php');
require('DBFolder.php');
require('xdb_files.php');
require('FileBlockWriter.php');
require('FolderInf.php');
require('PathTool.php');
//require('RedisTool.php');
require('tasks.php');
require('biz/PathBuilder.php');
require('biz/PathGuidBuilder.php');
require('biz/BlockPathBuilder.php');

$uid 			= $_GET["uid"];
$lenLoc			= $_GET["lenLoc"];//10240
$sizeLoc		= $_GET["sizeLoc"];//10mb
$sizeLoc		= str_replace("+", " ", $sizeLoc);
$callback 		= $_GET["callback"];//jsonp
$pathLoc		= $_GET["pathLoc"];
$idSign			= $_GET["idSign"];
$pathLoc		= PathTool::url_decode($pathLoc);

if(    strlen($uid)<1
	|| empty($sizeLoc))
{
	echo $callback . "({\"value\":null,\"inf\":\"参数为空，请检查uid,sizeLoc参数。\"})";
	die();
}

$ext = PathTool::getExtention($pathLoc);
$fileSvr = new xdb_files();
$fileSvr->idSign = $idSign;
$fileSvr->f_fdChild = false;
$fileSvr->f_fdTask = false;
$fileSvr->nameLoc = PathTool::getName($pathLoc);
$fileSvr->pathLoc = $pathLoc;
$fileSvr->nameSvr = $fileSvr->nameLoc;
$fileSvr->lenLoc = $lenLoc;
$fileSvr->sizeLoc = $sizeLoc;
$fileSvr->deleted = false;
$fileSvr->uid = intval($uid);

//生成路径
$pb = new PathGuidBuilder();
$fileSvr->pathSvr = $pb->genFile($uid,$fileSvr->nameLoc);
//生成文件块路径
$bpb = new BlockPathBuilder();
$fileSvr->blockPath = $bpb->root($idSign, $fileSvr->pathSvr);

//添加到redis
$r = RedisTool::con();
$taskSvr = new tasks($r);
$taskSvr->uid = $uid;
$taskSvr->add($fileSvr);
$r->close();

//fix:防止json_encode将汉字转换成unicode
$fileSvr->nameLoc = PathTool::urlencode_safe($fileSvr->nameLoc);
$fileSvr->pathLoc = PathTool::urlencode_safe($fileSvr->pathLoc);
	
$json = json_encode($fileSvr);//低版本php中，json_encode会将汉字进行unicode编码
$json = urldecode( $json );//还原汉字

$json = urlencode($json);
$json = str_replace("+","%20",$json);
$json = $callback . "({'value':'$json'})";//返回jsonp格式数据。
echo $json;
header('Content-Length: ' . ob_get_length());
?>