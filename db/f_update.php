<?php 
ob_start();
header('Content-Type: text/html;charset=utf-8');

require('DbHelper.php');
require('DBFile.php');
require('DBFolder.php');

$uid 		= $_GET["uid"];
$sign 		= $_GET["sign"];
$fid 		= $_GET["idSvr"];
$perSvr 	= $_GET["perSvr"];
$lenSvr 	= $_GET["lenSvr"];
$lenLoc 	= $_GET["lenLoc"];

if(    strlen(uid) < 1 
	|| strlen(lenLoc) < 1
	|| strlen(fid) <1 )
{
	echo "param is null";		
	die();
}

$db = new DBFile();
$db->f_process($uid,$fid,0,$lenSvr,$perSvr,false);

header('Content-Length: ' . ob_get_length());
?>