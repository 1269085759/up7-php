<?php
class FileRedis
{	
	var $con;

	function __construct($con)
	{
		$this->con = $con;
	}
	
	function create($f)
	{
		$j = $this->con;
		$j->hSet($f->signSvr,"nameLoc",$f->nameLoc);
		$j->hSet($f->signSvr,"pathLoc",$f->pathLoc);
		$j->hSet($f->signSvr,"pathSvr",$f->pathSvr);
		$j->hSet($f->signSvr,"lenLoc",$f->lenLoc);
		$j->hSet($f->signSvr,"lenSvr",$f->lenSvr);
		$j->hSet($f->signSvr,"sizeSvr",$f->sizeSvr);
		$j->hSet($f->signSvr,"perLoc",$f->perLoc);
		$j->hSet($f->signSvr,"fdTask",$f->folder);
	}
	
	function read($signSvr)
	{
		$j = $this->con;
		if( !$j->exists($signSvr)) return null;
		$f = new DnFileInf();
		$f->signSvr = $signSvr;
		$f->lenLoc = $j->hGet($signSvr,"lenLoc");
		$f->lenSvr = $j->hGet($signSvr,"lenSvr");
		$f->perLoc = $j->hGet($signSvr,"perLoc");
		$f->pathLoc = $j->hGet($signSvr,"pathLoc");
		$f->pathSvr = $j->hGet($signSvr,"pathSvr");
		$f->sizeSvr = $j->hGet($signSvr,"sizeSvr");
		$f->nameLoc = $j->hGet($signSvr,"nameLoc");
		$f->folder = strtolower($j->hGet($signSvr,"fdTask")) == "true";
	}
	
	function process($signSvr,$perLoc,$lenLoc,$sizeLoc)
	{
		$j = $this->con;
		$j->hSet($signSvr,"lenLoc",$lenLoc);
		$j->hSet($signSvr,"perLoc",$perLoc);
		$j->hSet($signSvr,"sizeLoc",$sizeLoc);
	}
}
?>