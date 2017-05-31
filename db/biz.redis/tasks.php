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
		$keys = $this->con->sMembers($this->getKey());
		if(is_null($keys)) return null;
		
		$cache = new FileRedis($this->con);
		$files = array();
		foreach($keys as $key)
		{
			$f = $cache->read($key);
			$f->pathLoc = PathTool::urlencode_safe($f->pathLoc);//防止json_encode将中文转换成unicode
			$f->pathSvr = PathTool::urlencode_safe($f->pathSvr);//防止json_encode将中文转换成unicode
			$f->pathRel = PathTool::urlencode_safe($f->pathRel);//防止json_encode将中文转换成unicode
			$f->nameLoc = PathTool::urlencode_safe($f->nameLoc);//防止json_encode将中文转换成unicode
			$f->nameSvr = PathTool::urlencode_safe($f->nameSvr);//防止json_encode将中文转换成unicode
			$files[] = $f;
		}
	}
	
	function toJson()
	{
		$fs = $this->all();
		$fs = urldecode($fs);//还原
		if(is_null($fs)) return "";
		
		return json_encode($fs);				
	}	
}
?>