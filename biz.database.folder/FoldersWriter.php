<?php

class FoldersWriter
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
		$sql = "insert into up7_folders(
				 fd_sign
				,fd_name
				,fd_pidSign
				,fd_uid
				,fd_length
				,fd_size
				,fd_pathLoc
				,fd_pathSvr
				,fd_folders
				,fd_files
				,fd_rootSign
				) values(
				 :fd_sign
				,:fd_name
				,:fd_pidSign
				,:fd_uid
				,:fd_length
				,:fd_size
				,:fd_pathLoc
				,:fd_pathSvr
				,:fd_folders
				,:fd_files
				,:fd_rootSign
				)";

		$cmd = $this->db->prepare_utf8( $sql );
		return $cmd;
	}
	
	function save($cmd,$f)
	{
		$cmd->bindValue(":fd_sign",$f->idSign,PDO::PARAM_STR);
		$cmd->bindValue(":fd_name",$f->nameLoc,PDO::PARAM_STR);
		$cmd->bindValue(":fd_pidSign",$f->pidSign,PDO::PARAM_STR);
		$cmd->bindValue(":fd_uid",$f->uid,PDO::PARAM_BOOL);
		$cmd->bindValue(":fd_length",$f->lenLoc,PDO::PARAM_INT);
		$cmd->bindValue(":fd_size",$f->sizeLoc,PDO::PARAM_STR);
		$cmd->bindValue(":fd_pathLoc",$f->pathLoc,PDO::PARAM_STR);
		$cmd->bindValue(":fd_pathSvr",$f->pathSvr,PDO::PARAM_STR);
		$cmd->bindValue(":fd_folders",0,PDO::PARAM_INT);
		$cmd->bindValue(":fd_files",$f->filesCount,PDO::PARAM_INT);
		$cmd->bindValue(":fd_rootSign","",PDO::PARAM_STR);
		$cmd->execute();
	}
	
	function saveAll()
	{
		$cmd = $this->makeCmd();
		//保存文件夹
		$this->save($cmd,$this->folder);
		/*
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
		*/
	}
}
?>