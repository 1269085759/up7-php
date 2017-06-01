<?php

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
	
	function delFd($idSign)
	{
		//清除文件缓存表
		$this->con->del("$idSign-files");
		$this->del($idSign);
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
			$f->pathLoc = PathTool::urlencode_path($f->pathLoc);//防止json_encode将中文转换成unicode
			$f->pathSvr = PathTool::urlencode_path($f->pathSvr);//防止json_encode将中文转换成unicode
			$f->pathRel = PathTool::urlencode_path($f->pathRel);//防止json_encode将中文转换成unicode
			$f->nameLoc = PathTool::urlencode_path($f->nameLoc);//防止json_encode将中文转换成unicode
			$f->nameSvr = PathTool::urlencode_path($f->nameSvr);//防止json_encode将中文转换成unicode
			$files[] = $f;
		}
		return $files;
	}
	
	function toJson()
	{
		$fs = $this->all();
		if(is_null($fs)) return "";
		$json = json_encode($fs);		
		$json = PathTool::url_decode($json);//还原汉字
		return $json;				
	}	
}
?>