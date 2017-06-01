<?php
class PathTool
{
	static function guid()
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
	
	static function getName($path)
	{		
		$arr = explode("\\",$path);
		$name= $arr[count($arr) - 1];
		return $name;
	}
	
	static function getExtention($path)
	{		
		$parts = pathinfo( $path);
		$ext = $parts["extension"];//ext,jpg,gif,exe
		//$ext = strtolower($ext);
		return $ext;
	}
	
	static function urlencode_safe($txt)
	{
		$txt = str_replace("\\", "/", $txt);//urlencode会将\\解析成\ 
		$txt = urlencode($txt);
		return str_replace("+","%20",$txt);
	}
	
	static function urlencode_path($txt)
	{
		$txt = str_replace("\\", "/", $txt);//urlencode会将\\解析成\
		$txt = str_replace("/", "\\\\", $txt); 
		$txt = urlencode($txt);
		$txt = str_replace("+","%20",$txt);
		return $txt;		
	}
	
	static function url_decode($txt)
	{
		$txt = str_replace("+","%20",$txt);
		$txt = urldecode($txt);
		return $txt;		
	}
	
	static function url_encode($txt)
	{
		$txt = urlencode($txt);
		$txt = str_replace("+", "%20", $txt);
		return $txt;
	}
	
	static function combin($p1,$p2)
	{
		$split = PathTool::endsWith($p1,"/");
		if(!$split) $split = PathTool::endsWith($p1,"\\");
		
		//以/结尾
		if($split)
		{
			return str_replace("\\","/","$p1$p2");
		}
		return str_replace("\\","/","$p1/$p2");
	}
	
	static function endsWith($str,$key)
	{
		$len = strlen($str);
		$last = $str;
		if($len > 1) $last = substr($str, $len-1);
		
		return strcmp($last, $key) == 0;
	}
	
	static function to_utf8($str)
	{
		$encode = mb_detect_encoding($str, array('ASCII','GB2312','GBK','UTF-8'));
		$encode = strtolower($encode);
		if( $encode == "utf-8" ) return $str;
		
		return iconv($encode, "UTF-8", $str);
	}
	
	static function to_gbk($str)
	{		
		$encode = strtolower( mb_detect_encoding($str, array('ASCII','GB2312','GBK','UTF-8')) );
		if( $encode != "utf-8" ) return $str;
		
		
		return iconv("utf-8", "gbk", $str);
	}
	
	function unicode_decode($str)
	{
		return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
				create_function(
						'$matches',
						'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
				),
				$str);
	}
	
}
?>