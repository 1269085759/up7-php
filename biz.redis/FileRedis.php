<?php

class FileRedis
{
	var $con;
	function __construct($r)
	{
		$this->con = $r;
	}
	
	function process($idSign,$perSvr,$lenSvr,$blockCount,$blockSize)
	{
		$r = $this->con;
		$r->hSet($idSign, "perSvr", $perSvr);
		$r->hSet($idSign, "lenSvr", $lenSvr);
		if( $blockCount != "0" )
			$r->hSet($idSign, "blockCount", $blockCount);
		
		if( $blockSize != "0" )
			$r->hSet($idSign, "blockSize", $blockSize);
	}
	
	function create($f)
	{
		$r = $this->con;
		if($r->exists($f->idSign)) return;
			
		$r->hSet($f->idSign, "fdTask", $f->folder==true?"true":"false");
		$r->hSet($f->idSign, "rootSign", $f->rootSign);
		$r->hSet($f->idSign, "pidSign", $f->pidSign);
		$r->hSet($f->idSign, "pathLoc", $f->pathLoc);
		$r->hSet($f->idSign, "pathSvr", $f->pathSvr);
		$r->hSet($f->idSign, "blockPath", $f->blockPath);
		$r->hSet($f->idSign, "nameLoc", $f->nameLoc);
		$r->hSet($f->idSign, "nameSvr", $f->nameSvr);
		$r->hSet($f->idSign, "lenLoc", strval($f->lenLoc) );
		$r->hSet($f->idSign, "lenSvr", "0" );
		$r->hSet($f->idSign, "blockCount", strval($f->blockCount) );
		$r->hSet($f->idSign, "sizeLoc",$f->sizeLoc);
		$r->hSet($f->idSign, "filesCount", strval($f->filesCount) );
		$r->hSet($f->idSign, "foldersCount", "0" );
	}
	
	function read($idSign)
	{
		$r = $this->con;
		if( !$r->exists($idSign)) return null;
		
		$f = new xdb_files();
		$f->idSign 		= $idSign;
		$f->rootSign 	= $r->hGet($idSign, "rootSign");
		$f->pidSign 	= $r->hGet($idSign, "pidSign");
		$f->pathLoc 	= $r->hGet($idSign, "pathLoc");
		$f->pathSvr 	= $r->hGet($idSign, "pathSvr");
		$f->blockPath  	= $r->hGet($idSign, "blockPath");
		$f->nameLoc 	= $r->hGet($idSign, "nameLoc");
		$f->nameSvr 	= $r->hGet($idSign, "nameSvr");
		$f->lenLoc 	 	= $r->hGet($idSign, "lenLoc");
		$f->sizeLoc 	= $r->hGet($idSign, "sizeLoc");
		$f->lenSvr 	 	= $r->hGet($idSign, "lenSvr");
		$f->perSvr 	 	= $r->hGet($idSign, "perSvr");
		$f->blockCount 	= intval($r->hGet($idSign, "blockCount"));
		$blockSize 		= $r->hGet($idSign, "blockSize");
		if(null == $blockSize) $blockSize="0";
		$f->blockSize 	= intval($blockSize);
		$f->filesCount 	= intval($r->hGet($idSign, "filesCount"));
		$f->folder		= strcasecmp($r->hGet($idSign,"fdTask"),"true")==0;
		return $f;
	}	
	
}
?>