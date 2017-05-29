<?php
ob_start();
header('Content-Type: text/html;charset=utf-8');
require('biz/HttpHeader.php');
/*
	此文件只负责将数据表中文件上传进度更新为100%
		向数据库添加新记录在 ajax_create_fid.php 文件中处理
	如果服务器不存在此文件，则添加一条记录，百分比为100%
	如果服务器已存在相同文件，则将文件上传百分比更新为100%
*/
//$txt = "MyEclipse8.5\\u6c49\\u5316.doc";
$nid = 0;
$head = new HttpHeader();
echo $head->param("Accept-Language");



//header('Content-Length: ' . ob_get_length());
?>