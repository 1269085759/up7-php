<?php
/*
 * 
	更新记录：
		2015-05-13 创建
		2017-06-01 修改
*/
require('../../biz.redis/RedisTool.php');
require('../biz.redis/tasks.php');

$j = RedisTool::con();
$svr = new tasks("0", $j);
$svr->clear();
$j->close();
?>