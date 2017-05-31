<?php
class PathGuidBuilder extends PathBuilder
{
	function guid()
	{
		$ret = "";
		if (function_exists('com_create_guid'))
		{
			$ret = com_create_guid();
		}
		else
		{
			mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);// "-"
			$uuid = chr(123)// "{"
					.substr($charid, 0, 8).$hyphen
					.substr($charid, 8, 4).$hyphen
					.substr($charid,12, 4).$hyphen
					.substr($charid,16, 4).$hyphen
					.substr($charid,20,12)
					.chr(125);// "}"
			$ret = $uuid;
		}
		$ret = str_replace("{","",$ret);
		$ret = str_replace("}","",$ret);
		$ret = str_replace("-","",$ret);
		$ret = strtolower($ret);
		return $ret;
	}
	
	//d:\\wamp\\www\\up6\\upload\\
	function genFolder($uid,$nameLoc)
	{
		date_default_timezone_set("PRC");//设置北京时区
		$path = $this->getRoot();
		$path = PathTool::combin($path, date("Y"));
		$path = PathTool::combin($path, date("m"));
		$path = PathTool::combin($path, date("d"));
		$path = PathTool::combin($path,$this->guid());
		$path = PathTool::combin($path,$nameLoc);
		
		$path_gbk = iconv("utf-8","gbk",$path);
		if( !is_dir($path_gbk)) mkdir($path_gbk,0777,true);
		
		return $path;
	}

	/**
	 * 返回文件路径，自动创建目录
	 * /upload/年/月/日/guid/QQ2013.exe
	 * @param $uid
	 * @param $nameLoc
	 * @return mixed
	 */
	function genFile($uid,$nameLoc)
	{
		date_default_timezone_set("PRC");//设置北京时区
		$path = $this->getRoot();
		$path = PathTool::combin($path, date("Y"));
		$path = PathTool::combin($path, date("m"));
		$path = PathTool::combin($path, date("d"));
		$path = PathTool::combin($path,$this->guid());

		//在windows平台需要转换成多字节编码
		$path_gbk = PathTool::to_gbk($path);		
		if(!is_dir($path_gbk)) mkdir($path_gbk,0777,true);

		$path = PathTool::combin($path,$nameLoc);
		return $path;
	}
	
	function createFolder($v)
	{
		$path = PathTool::to_gbk($v);//在windows环境中需要转换成gbk
		if( !is_dir($path)) mkdir($path,0777,true);
		return PathTool::to_utf8( realpath($path) );//规范化路径
	}
}
?>