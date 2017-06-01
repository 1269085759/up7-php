<?php
header('Content-Type: text/html;charset=utf-8');
require('../../biz/PathTool.php');

$uid = $_GET["uid"];
$cbk = $_GET["callback"];//jsonp

if ( strlen($uid) > 0)
{
	$cb = new cmp_builder();
	$json = $cb->read($uid);	
	if(!empty($json))
	{
		$json = urlencode($json);
		$json = str_replace("+", "%20", $json);
		echo "$cbk({\"value\":\"$json\"})";
		return;
	}
}
echo $cbk . "({\"value\":null})";
?>