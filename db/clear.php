<?php

require('DbHelper.php');
require('inc.php');
require('DBFile.php');
require('FileInf.php');
require('FolderInf.php');
require('tasks.php');

DBFile::Clear();
echo "数据库清除成功<br/>";
$r = RedisTool::con();
$t = new tasks($r);
$t->clear();
$r->close();
echo "redis缓存清除成功<br/>";
?>