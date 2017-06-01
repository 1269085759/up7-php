<?php
header('Content-Type: text/html;charset=utf-8');
require('../../biz/PathTool.php');
require('../../biz.database/DbHelper.php');
require('../biz/CompleteReader.php');

$uid = $_GET["uid"];
$cbk = $_GET["callback"];//jsonp

if ( strlen($uid) > 0)
{
	$cb = new CompleteReader();
	$json = $cb->all($uid);		
	if( !empty($json) )
	{
		$json = PathTool::url_encode($json);		
		echo "$cbk({\"value\":\"$json\"})";
		return;
	}
}
echo $cbk . "({\"value\":null})";
?>