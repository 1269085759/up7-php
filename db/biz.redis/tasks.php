<?php
require('FileRedis.php');

class tasks
{
	var $uid = "";
	var $keyName = "tasks";
	var $con;
	function __construct($r)
	{
		$this->con = $r;
	}
	
	function getKey()
	{
		return $this->uid . "-" . $this->keyName;
	}
	
	function sadd($sign)
	{
		$this->con->sAdd($this->getKey(), $sign);
	}
	
	/**
	 * 将文件信息添加到缓存
	 * 将文件idSign添加到任务列表
	 * @param f
	 */
	function add($f)
	{
		//添加到任务列表
		$this->sadd($f->idSign);
	
		//添加key
		$fs = new FileRedis($this->con);
		$fs->create($f);
	}
	
	function del($sign)
	{
		//从队列中删除
		$this->con->srem($this->getKey(), $sign);
		//删除key
		$this->con->del($sign);
	}
	
	function clear()
	{
		$this->con->flushDB();
	}
	
	function all()
	{		
	}
	
	function toJson()
	{
		$con = $this->con;
		$con= new Redis();
		$con->sMembers($this->getKey());				
	}	
}
?>