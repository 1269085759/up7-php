<?php

require('biz.database/DbHelper.php');
require('biz.database/DBFile.php');
require('biz.redis/tasks.php');
require('biz.redis/RedisTool.php');

DBFile::Clear();
echo "数据库清除成功<br/>";
$r = RedisTool::con();
$t = new tasks($r);
$t->clear();
$r->close();
echo "redis缓存清除成功<br/>";
?>