<?php
/**
 * 
 * @author zysoft qwl
 * 更新记录：
 *   2017-05-29 创建
 *   2017-05-31 完善对中文路径的支持
 *
 */
class FilePart
{
	/**
	 * 
	 * @param $partPath 块路径
	 * @param $pathTmp 临时文件路径
	 */
	function save($partPath,$pathTmp)
	{
		$path = iconv("utf-8","gbk",$partPath);
		
		//创建层级目录
		$fd = dirname($path);
		if( !is_dir($fd)) mkdir($fd,0777,true);
		
		//移动文件块
		rename($pathTmp,$path);
	}
}
?>