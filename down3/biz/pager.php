<?php
class pager
{	
	function read($pageIndex,$id)
	{
		$pageSize = 100;
		$pageStart = ((intval($pageIndex)-1)*$pageSize)+1;
		$sql = "select f_nameLoc,f_pathLoc,f_pathSvr,f_pathRel,f_blockPath,f_blockSize,f_lenLoc,f_sizeLoc from up7_files where f_rootSign='$id' limit $pageStart, $pageSize";
		//
		$db = new DbHelper();
		$cmd = $db->prepare_utf8($sql);
		$ret = $db->ExecuteDataSet($cmd);
		$files = array();

		foreach($ret as $row)
		{
			$f = new DnFileInf();			
			$f->nameLoc 	= $row["f_nameLoc"];
			$f->pathLoc 	= $row["f_pathLoc"];
			$f->pathSvr 	= $row["f_pathSvr"];
			$f->pathRel 	= $row["f_pathRel"];
			$f->blockPath 	= $row["f_blockPath"];
			$f->blockSize	= $row["f_blockSize"];
			$f->lenLoc 		= $row["f_lenLoc"];
			$f->sizeLoc 	= $row["f_sizeLoc"];			

			$f->nameLoc = PathTool::url_encode($f->nameLoc);
			$f->pathLoc = PathTool::urlencode_path($f->pathLoc);
			$f->pathSvr = PathTool::urlencode_path($f->pathSvr);
			$f->pathRel = PathTool::urlencode_path($f->pathSvr);
			$f->blockPath = PathTool::urlencode_path($f->blockPath);
			$f->pathSvr = str_replace("\\", "/", $f->pathSvr);
			$f->pathRel = str_replace("\\", "/", $f->pathRel);
			$f->blockPath = str_replace("\\", "/", $f->blockPath);
			$files[] = $f;
		}
		$json = json_encode($files);
		$json = PathTool::url_decode($json);//还原汉字
		return $json;
	}
}
?>