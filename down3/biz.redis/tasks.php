<?php
class tasks
{	
	var $uid;
	var $con;
	
	function __construct($uid,$con)
	{ 
		$this->uid = $uid;
		$this->con = $con;
	}

	function Clear()
	{
		$keys = $this->con->keys("d-*");
		foreach($keys as $k)
		{
			$this->clearUser($k);
		}
	}
	
	function clearUser($key)
	{
		$len = $this->con->sCard($key);
		while($len > 0)
		{
			$keys = $this->con->sRandMember($key,500);
			$len -= count($keys);
			foreach($keys as $k)
			{
				$this->con->del($k);
			}
		}
	}
	
	function add($f/*DnFileInf*/)
	{
		$svr = new FileRedis($this->con);
		$svr->create($f);
		
		$km = new KeyMaker();
		$space = $km->space($this->uid);
		$this->con->sAdd($space,$f->signSvr);
	}
	
	function del($signSvr)
	{
		$km = new KeyMaker();
		$space = $km->space($this->uid);
		
		$this->con->sRem($space,$signSvr);
		
		$this->con->del($signSvr);
	}
	
	function toJson()
	{
		$km = new KeyMaker();
		$space = $km->space($this->uid);
		$keys = $this->con->sMembers($space);
		$files = array();
		$svr = new FileRedis($this->con);
		foreach($keys as $k)
		{
			$f = $svr->read($k);
			$f->nameLoc = PathTool::url_encode($f->nameLoc);
			$f->pathLoc = PathTool::urlencode_path($f->pathLoc);
			$f->pathSvr = PathTool::urlencode_path($f->pathSvr);
			$f->pathSvr = str_replace("\\", "/", $f->pathSvr);
			$f->pathRel = PathTool::urlencode_path($f->pathRel);
			$files[] = $f;			
		}
		$json = json_encode($files);
		$json = PathTool::url_decode($json);//还原汉字
	}
}
?>