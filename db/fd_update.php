<?php 
ob_start();
header('Content-Type: text/html;charset=utf-8');

require('../DbHelper.php');
require('../DBFile.php');
require('../DBFolder.php');

$uid 		= $_GET["uid"];
$sign 		= $_GET["sign"];
$fid 		= $_GET["idSvr"];
$perSvr 	= $_GET["perSvr"];
$lenSvr 	= $_GET["lenSvr"];
$lenLoc 	= $_GET["lenLoc"];

if(    strlen(uid) < 1 
	|| strlen(lenSvr) < 1
	|| strlen(fid) <1
	|| strlen(perSvr) <1 )
{
	echo "param is null";		
	die();
}

$db = new DBFolder();
$db->update($fid,$perSvr,$lenSvr,$uid);

header('Content-Length: ' . ob_get_length());
?>