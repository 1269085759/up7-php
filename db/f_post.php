<?php
ob_start();
/*
	控件每次向此文件POST数据
	逻辑：
		1.更新数据库进度
		2.将文件块数据保存到服务器中。
	更新记录：
		2014-04-09 增加文件块验证功能。
		2017-05-29 完善逻辑
*/
require('../biz.database/DbHelper.php');
require('../biz.database/DBFile.php');
require('../biz.model/xdb_files.php');
require('../biz.redis/RedisTool.php');
require('../biz.redis/FileRedis.php');
require('../biz/HttpHeader.php');
require('../biz/PathTool.php');
require('../biz/FilePart.php');
require('../biz/BlockPathBuilder.php');

$head = new HttpHeader();
$uid 			= $head->param("f-uid");
$idSign 		= $head->param("f-idSign");
$perSvr 		= $head->param("f-perSvr");
$lenSvr			= $head->param("f-lenSvr");
$lenLoc			= $head->param("f-lenLoc");
$nameLoc		= $head->param("f-nameLoc");
$pathLoc		= $head->param("f-pathLoc");
$sizeLoc		= $head->param("f-sizeLoc");
$f_pos 			= $head->param("f-RangePos");
$rangeIndex		= $head->param("f-rangeIndex");
$rangeCount		= $head->param("f-rangeCount");
$rangeSize		= $head->param("f-rangeSize");
$complete		= "false";
$fd_idSign		= $head->param("fd-idSign");
$fd_lenSvr		= $head->param("fd-lenSvr");
$fd_perSvr		= $head->param("fd-perSvr");
$pathLoc		= PathTool::url_decode($pathLoc);
$nameLoc		= PathTool::url_decode($nameLoc);
$fpath			= $_FILES['file']['tmp_name'];//

//相关参数不能为空
if (   (strlen($lenSvr)>0) 
	&& (strlen($uid)>0) 
	&& (strlen($idSign)>0) 
	&& (strlen($f_pos)>0))
{		
	$svr = RedisTool::con();
	$cache = new FileRedis($svr);
	$bpb = new BlockPathBuilder();
	
	//文件块
	if( is_null($fd_idSign))
	{
		$fileSvr = $cache->read($idSign);
		//生成块路径
		$partPath = $bpb->part($idSign, $rangeIndex, $fileSvr->pathSvr);
		
		//保存文件块
		$part = new FilePart();
		$part->save($partPath, $fpath);
		
		//更新进度
		if(strcmp($f_pos, "0") == 0) $cache->process($idSign, $perSvr, $lenSvr, $rangeCount, $rangeSize);
	}//子文件块
	else
	{
		$fd = $cache->read($fd_idSign);//文件夹信息
		$childSvr = new xdb_files();
		$childSvr->idSign = $idSign;
		$childSvr->nameLoc = $nameLoc;
		$childSvr->nameSvr = $nameLoc;
		$childSvr->lenLoc = $lenLoc;
		$childSvr->sizeLoc = $sizeLoc;
		$childSvr->pathLoc = str_replace("\\", "/", $pathLoc);
		$childSvr->pathSvr = str_replace($fd->pathLoc, $fd->pathSvr, $pathLoc);
		$childSvr->pathRel = str_replace("$fd->pathLoc\\", "", $pathLoc);
		$childSvr->pathSvr = str_replace("\\", "/", $childSvr->pathSvr);//将服务端路径转换为/格式,以支持linux环境
// 		$childSvr->pathRel = str_replace("\\", "/", $childSvr->pathRel);//将相对路径转换为/格式
		$childSvr->rootSign = $fd_idSign;
		$childSvr->blockCount = $rangeCount;
		$childSvr->blockSize = $rangeSize;
		//子文件块路径
		$childSvr->blockPath = $bpb->rootFD($childSvr, $rangeIndex, $fd);
		$partPath = PathTool::combin($childSvr->blockPath, "$rangeIndex.part");
		//保存块
		$part = new FilePart();
		$part->save($partPath, $fpath);
		//添加到缓存
		if( !$svr->exists($idSign) )
		{
			$cache->create($childSvr);
			//添加到文件夹
			$svr->lPush($fd->idSign."-files",$idSign);
		}//更新文件夹进度
		else if(strcmp($f_pos,"0")==0 )
		{
			$cache->process($fd_idSign, $fd_perSvr, $fd_lenSvr, "0", "0");			
		}
		
	}	
	echo "ok";
}
else
{
	echo "param is null";
}
header('Content-Length: ' . ob_get_length());
?>