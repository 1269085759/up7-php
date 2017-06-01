<?php
class CompleteReader
{	
	function all($uid)
	{
		$sql = "select
				 f_idSign
				,f_nameLoc
				,f_lenLoc
				,f_sizeLoc
				,f_fdTask
				,f_pathLoc
				,f_pathSvr
				,f_blockPath
				,f_blockSize
				,fd_files
				 from up7_files
				 left join up7_folders on up7_folders.fd_sign=up7_files.f_idSign
				 where f_uid=$uid and f_complete=1 and f_fdChild=0";
		//
		$db = new DbHelper();
		$cmd = $db->prepare_utf8($sql);
		$ret = $db->ExecuteDataSet($cmd);
		$files = array();

		foreach($ret as $row)
		{
			$f = new DnFileInf();
			$f->idSign 		= $row["f_idSign"];
			$f->nameLoc 	= $row["f_nameLoc"];
			$f->lenSvr 		= $row["f_lenLoc"];
			$f->pathLoc 	= $row["f_pathLoc"];
			$f->pathSvr 	= $row["f_pathSvr"];
			$f->blockPath 	= $row["f_blockPath"];
			$f->blockSize	= $row["f_blockSize"];
			$f->sizeSvr 	= $row["f_blockSize"];
			$f->folder		= strtolower( $row["f_fdTask"] ) == "true";
			$f->signSvr 	= $row["f_idSign"];
			$f->fileCount 	= intval($row["fd_files"]);

			$f->nameLoc = PathTool::url_encode($f->nameLoc);
			$f->pathLoc = PathTool::urlencode_path($f->pathLoc);
			$f->pathSvr = PathTool::urlencode_path($f->pathSvr);
			$f->blockPath = PathTool::urlencode_path($f->blockPath);
			$f->pathSvr = str_replace("\\", "/", $f->pathSvr);
			$f->blockPath = str_replace("\\", "/", $f->blockPath);
			$files[] = $f;
		}
		$json = json_encode($files);
		$json = PathTool::url_decode($json);//还原汉字
		return $json;
	}
}
?>