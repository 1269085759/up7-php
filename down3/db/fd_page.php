<?php
ob_start();
header('Content-Type: text/html;charset=utf-8');
require('../../biz.database/DbHelper.php');
require('../../biz/PathTool.php');
require('../biz.model/DnFileInf.php');
require('../biz/pager.php');
/*
 * 分页数据
	更新记录：
		2015-05-13 创建
		2016-07-29 更新
*/
$idSign = $_GET["idSign"];
$page	= $_GET["page"];

if ( 	$idSign== ""
	||	$page==""
	)
{
	header('HTTP/1.1 500 param is null');	
	return;
}

$pg = new pager();
$json = $pg->read($page, $idSign);
$json = PathTool::url_encode($json);
echo $json;
header('Content-Length: ' . ob_get_length());
?>