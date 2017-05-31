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
		$cmd->bindValue(":f_idSign",$f->idSign,PDO::PARAM_STR);
		$cmd->bindValue(":f_pidSign",$f->pidSign,PDO::PARAM_STR);
		$cmd->bindValue(":f_rootSign",$f->rootSign,PDO::PARAM_STR);
		$cmd->bindValue(":f_fdChild",$f->f_fdChild,PDO::PARAM_BOOL);
		$cmd->bindValue(":f_uid",$f->uid,PDO::PARAM_INT);
		$cmd->bindValue(":f_nameLoc",$f->nameLoc,PDO::PARAM_STR);
		$cmd->bindValue(":f_nameSvr",$f->nameLoc,PDO::PARAM_STR);
		$cmd->bindValue(":f_pathLoc",$f->pathLoc,PDO::PARAM_STR);
		$cmd->bindValue(":f_pathSvr",$f->pathSvr,PDO::PARAM_STR);
		$cmd->bindValue(":f_pathRel",$f->pathRel,PDO::PARAM_STR);
		$cmd->bindValue(":f_lenLoc",$f->lenLoc,PDO::PARAM_INT);
		$cmd->bindValue(":f_sizeLoc",$f->sizeLoc,PDO::PARAM_STR);
		$cmd->bindValue(":f_lenSvr",$f->lenSvr,PDO::PARAM_INT);
		$cmd->bindValue(":f_perSvr",$f->perSvr,PDO::PARAM_STR);
		$cmd->bindValue(":f_sign",$f->sign,PDO::PARAM_STR);
		$cmd->bindValue(":f_complete",true,PDO::PARAM_BOOL);
		$cmd->bindValue(":f_fdTask",$f->folder,PDO::PARAM_BOOL);
		$cmd->bindValue(":f_blockCount",$f->blockCount,PDO::PARAM_INT);
		$cmd->bindValue(":f_blockSize",$f->blockSize,PDO::PARAM_INT);
		$cmd->bindValue(":f_blockPath",$f->blockPath,PDO::PARAM_STR);
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
				$f = $svr->read($k);
				$f->f_fdChild = true;
				$this->save($cmd, $f);
				
				//清除文件缓存
				$this->cache->del($k);
			}
		}
	}
}
?>