<?php
class RedisTool
{
	static function con()
	{
		$redis = new Redis();
		$redis->connect('127.0.0.1',6379);
		return $redis;
	}
	
	
	
	
	
	
	
	
	
}
?>