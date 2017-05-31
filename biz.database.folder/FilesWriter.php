<?php

class FilesWriter
{		
	var $db;
	var $cache;
	var $folder;
	
	function __construct($cache,$fd/*xdb_files*/)
	{
		$this->db = new DbHelper();
		$this->folder = $fd;
		$this->cache = $cache;
	}
	
	function makeCmd()
	{
		$sql = "insert into up7_files(
				 f_idSign
				,f_pidSign
				,f_rootSign
				,f_fdChild
				,f_uid
				,f_nameLoc
				,f_nameSvr
				,f_pathLoc
				,f_pathSvr
				,f_pathRel
				,f_lenLoc
				,f_sizeLoc
				,f_lenSvr
				,f_perSvr
				,f_sign
				,f_complete
				,f_fdTask
				,f_blockCount
				,f_blockSize
				,f_blockPath
				) values(
				 :f_idSign
				,:f_pidSign
				,:f_rootSign
				,:f_fdChild
				,:f_uid
				,:f_nameLoc
				,:f_nameSvr
				,:f_pathLoc
				,:f_pathSvr
				,:f_pathRel
				,:f_lenLoc
				,:f_sizeLoc
				,:f_lenSvr
				,:f_perSvr
				,:f_sign
				,:f_complete
				,:f_fdTask
				,:f_blockCount
				,:f_blockSize
				,:f_blockPath
				)";

		$cmd = $this->db->prepare_utf8( $sql );
		return $cmd;
	}
	
	function save($cmd,$f)
	{
		$cmd->bindValue(":f_idSign",$f->idSign);
		$cmd->bindValue(":f_pidSign",$f->pidSign);
		$cmd->bindValue(":f_rootSign",$f->rootSign);
		$cmd->bindValue(":f_fdChild",$f->f_fdChild,PDO::PARAM_BOOL);
		$cmd->bindValue(":f_uid",$f->uid,PDO::PARAM_INT);
		$cmd->bindValue(":f_nameLoc",$f->nameLoc);
		$cmd->bindValue(":f_nameSvr",$f->nameLoc);
		$cmd->bindValue(":f_pathLoc",$f->pathLoc);
		$cmd->bindValue(":f_pathSvr",$f->pathSvr);
		$cmd->bindValue(":f_pathRel",$f->pathRel);
		$cmd->bindValue(":f_lenLoc",$f->lenLoc);
		$cmd->bindValue(":f_sizeLoc",$f->sizeLoc);
		$cmd->bindValue(":f_lenSvr",$f->lenSvr);
		$cmd->bindValue(":f_perSvr",$f->perSvr);
		$cmd->bindValue(":f_sign",$f->sign);
		$cmd->bindValue(":f_complete",true);
		$cmd->bindValue(":f_fdTask",$f->folder);
		$cmd->bindValue(":f_blockCount",$f->blockCount);
		$cmd->bindValue(":f_blockSize",$f->blockSize);
		$cmd->bindValue(":f_blockPath",$f->blockPath);
		$cmd->execute();
	}
	
	function saveAll()
	{
		$cmd = $this->makeCmd();
		//保存文件夹
		$this->save($cmd,$this->folder);
		$id = $this->folder->idSign;
		$key = "$id-files";
		
		$index = 0;
		$len = $this->cache->lLen($key);
		$svr = new FileRedis($this->cache);
		
		while($index < $len)
		{
			$keys = $this->cache->lRange($key,$index,$index+100);
			$index += count($keys);
			foreach($keys as $k)
			{
				$f = $svr->read($key);
				$f->f_fdChild = true;
				$this->save($cmd, $f);
				
				//清除文件缓存
				$this->cache->del($k);
			}
		}
	}
}
?>