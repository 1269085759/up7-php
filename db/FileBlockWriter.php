<?php
/*
	文件续传对象。负责将文件块写到服务器指定目录中
	使用方法：
		$resumer = new FileBlockWriter();
		$resumer->Resumer();
		
	更新记录：
		2012-03-30 创建
		2014-03-21 取消创建临时文件的操作，减少一次系统IO，直接读取临时文件。
		2014-04-09 
			修复Resumer方法中调用unlink($tmpPath);警告。
			新增CreateFile方法。
			增加文件块验证功能。
		2015-03-16
			修改创建文件的逻辑。将按实际文件大小创建一个大文件改为只创建一个字节的小文件，减少用户等待时间。
*/
class FileBlockWriter
{
	var $m_FilePath;	//文件路径
	var $m_FileTemp;	//临时文件名称
	var $m_NameRemote;	//远程文件名称
	var $m_FileSize;	//文件大小
	var $m_RangePos;	//文件起始位置
	var $m_RangeSize;	//文件块大小
	var $m_rangMD5;		//文件块的MD5值，用来做校验。
	var $m_pathSvr;
	
	function __construct($fpath="",$fsize="0",$rangPos="0",$pathSvr="") 
	{
		//如果取值为空，请检查php.ini文件中upload_tmp_dir 配置是否为空。设置临时文件夹后必须要设置Everyone读写权限
		$this->m_FileTemp	= $fpath;//临时文件完整路径
		$this->m_FileSize	= intval($fsize);
		$this->m_RangePos	= intval($rangPos);
		$this->m_RangeSize	= filesize($this->m_FileTemp);//获取临时文件大小
		//$this->m_pathSvr	= $pathSvr;
		$this->m_pathSvr = iconv("UTF-8","GB2312", $pathSvr);
	}
	
	//获取临时文件大小
	function GetRangeSize()
	{
		return $this->m_RangeSize;
	}

	//创建文件,f_create.php调用
	function make($path,$len)
	{
		$encode = mb_detect_encoding($path, array('ASCII','GB2312','GBK','UTF-8'));
		if( $encode == "UTF-8" ) $path = iconv( "UTF-8","GB2312",$path);		
		
		//创建层级目录
		$fd = dirname($path);
		if( !is_dir($fd)) mkdir($fd,0777,true);
		
		$hfile = fopen($path,"wb");
		//以实际大小创建文件，注意win32,win64中不支持2G+文件，因为php系统中的intval不支持int64
		ftruncate($hfile,$len);
		fclose($hfile);
	}
	
	/*
		续传文件块
		逻辑：
			1.根据文件MD5获取服务器文件完整地址。
			2.将文件块写入服务器文件中
		参数：
			$md5 文件MD5。
	*/
	function write($path,$offset,$rangeFile)
	{	
		$path = iconv( "UTF-8","GB2312",$path);
		$offset = intval($offset);
	
		//读取文件块数据
		$fHandle = fopen($rangeFile,"rb");
		$tempData = fread($fHandle,filesize($rangeFile));
		fclose($fHandle);
		
		//写入数据
		$hfile = fopen($path,"r+b");
		//定位到续传位置
		fseek($hfile, $offset,SEEK_SET);
		fwrite($hfile,$tempData);
		fclose($hfile);
		
		//删除临时文件
		//unlink($tmpPath);
	}
	
	//定位超过2G的文件
	function fseek64(&$fh, $offset)
	{
		fseek($fh, 0, SEEK_SET);
	
		if ($offset <= PHP_INT_MAX)
		{
			return fseek($fh, $offset, SEEK_SET);
		}
	
		$t_offset   = PHP_INT_MAX;
		$offset     = $offset - $t_offset;
	
		while (fseek($fh, $t_offset, SEEK_CUR) === 0)
		{
			if ($offset > PHP_INT_MAX)
			{
				$t_offset   = PHP_INT_MAX;
				$offset     = $offset - $t_offset;
			}
			else if ($offset > 0)
			{
				$t_offset   = $offset;
				$offset     = 0;
			}
			else
			{
				return 0;
			}
		}
	
		return -1;
	}
}
?>