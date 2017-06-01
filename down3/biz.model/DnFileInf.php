<?php
class DnFileInf
{
	var $idSvr = 0;
	var $idSign = "";
	var $signSvr= "";
	var $rootSign="";
	var $pidSign="";
	var $nameLoc = "";
	var $nameSvr = "";
	var $pathLoc = "";
	var $pathSvr = "";
	var $pathRel = "";
	var $blockPath = "";
	var $blockSize = 0;
	var $blockCount=0;
	var $uid = 0;
	var $fileUrl = "";
	var $lenLoc = "0";
	var $lenSvr = "0";
	var $sizeLoc="0byte";
	var $sizeSvr = "0byte";
	var $perLoc = "0%";
	var $complete = false;
	var $fdID = 0;//与up6_folder.fd_id对应
	var $folder = false;
	var $pidRoot = 0; 
	var $files = null;
	var $fileCount = 0;
	
	function __construct()
	{
		$this->signSvr = PathTool::guid();
	}
}
?>