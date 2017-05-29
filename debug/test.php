<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>

<body>
<?php
$mac = hash_hmac('sha1', 'eyJzY29wZSI6Im5jbWVtIiwiZGVhZGxpbmUiOjE0MzU3MTU2MzZ9', 's81HXCkwT6_8W9W1gzLOxiwedryqKkel3UU7npVR');
echo "hex值：$mac<br/>";
$mac = hash_hmac('sha1', 'eyJzY29wZSI6Im5jbWVtIiwiZGVhZGxpbmUiOjE0MzU3MTU2MzZ9', 's81HXCkwT6_8W9W1gzLOxiwedryqKkel3UU7npVR',true);
echo "hash_hmac值：$mac<br/>";
$mac = base64_encode($mac);
echo "线上SHA1值：Yx4nBSlS1oW+MpYAFNhz5redj+0=<br/>";
echo "本地SHA1值：$mac<br/>";
//echo phpversion();
/*$redis=new Redis();
$redis->connect('127.0.0.1',6379);
$redis->auth('123456');
$redis->set('test','hello world');
echo $redis->get('test');
echo phpinfo();*/

$r = new Redis();
$r->connect('127.0.0.1',6379);

$r->hSet("tt", "fdTask", "true");
$r->hSet("tt", "rootSign", 1);
$r->hSet("tt", "pathLoc", 1);
$r->hSet("tt", "pathSvr", 1);
$r->hSet("tt", "nameLoc", 1);
$r->hSet("tt", "nameSvr", 1);
$r->hSet("tt", "lenLoc", 1);
$r->hSet("tt", "lenSvr", "0" );
$r->hSet("tt", "blockCount", 1);
$r->hSet("tt", "sizeLoc",1);
$r->hSet("tt", "filesCount",1);
$r->hSet("tt", "foldersCount", "0");

$test = $r->hGetAll("tt");
var_dump($test);
var_dump("执行del");
$r->del("tt");
$test = $r->hGetAll("tt");
var_dump($test);

$r->flushDB();

$test1 = $r->hGetAll("tt");
var_dump($test1);

$r->close();
?>
</body>
</html>