<?php
/*
	说明：
		1.在调用此函数前不能有任何输出操作。比如 echo print
		
	更新记录：
		2012-04-03 创建
		2014-08-11 更新数据库操作代码。 
		2017-05-31 精简代码
*/
class DBFile
{
	var $db;//全局数据库连接,共用数据库连接
		
	function __construct() 
	{
		$this->db = new DbHelper();
	}
	
	function addComplete(&$inf/*xdb_files*/)
	{
		$sb = "insert into up7_files(";
		$sb = $sb . " f_idSign";
		$sb = $sb . ",f_uid";
		$sb = $sb . ",f_nameLoc";
		$sb = $sb . ",f_nameSvr";
		$sb = $sb . ",f_pathLoc";
		$sb = $sb . ",f_pathSvr";
		$sb = $sb . ",f_lenLoc";
		$sb = $sb . ",f_lenSvr";
		$sb = $sb . ",f_perSvr";
		$sb = $sb . ",f_sizeLoc";
		$sb = $sb . ",f_complete";
		$sb = $sb . ",f_blockCount";
		$sb = $sb . ",f_blockSize";
		$sb = $sb . ",f_blockPath";
	
		$sb = $sb . ") values (";
	
		$sb = $sb . " :f_idSign";//"@f_idSign";
		$sb = $sb . ",:f_uid";//",@f_uid";
		$sb = $sb . ",:f_nameLoc";//",@f_nameLoc";
		$sb = $sb . ",:f_nameSvr";//",@f_nameSvr";
		$sb = $sb . ",:f_pathLoc";//",@f_pathLoc";
		$sb = $sb . ",:f_pathSvr";//",@f_pathSvr";
		$sb = $sb . ",:f_lenLoc";//",@f_lenLoc";
		$sb = $sb . ",:f_lenSvr";//",@f_lenSvr";
		$sb = $sb . ",'100%'";//",@f_perSvr";
		$sb = $sb . ",:f_sizeLoc";//",@f_sizeLoc";
		$sb = $sb . ",1";//",@f_complete";
		$sb = $sb . ",:f_blockCount";//",@f_blockCount";
		$sb = $sb . ",:f_blockSize";//",@f_blockSize";
		$sb = $sb . ",:f_blockPath";//",@f_blockSize";
		$sb = $sb . ")";
	
		$db = &$this->db;
		$cmd = $db->prepare_utf8( $sb );
	
		$cmd->bindParam(":f_idSign",$inf->idSign);
		$cmd->bindValue(":f_uid",$inf->uid,PDO::PARAM_INT);
		$cmd->bindParam(":f_nameLoc",$inf->nameLoc);
		$cmd->bindParam(":f_nameSvr",$inf->nameLoc);
		$cmd->bindParam(":f_pathLoc",$inf->pathLoc);
		$cmd->bindParam(":f_pathSvr",$inf->pathSvr);
		$cmd->bindValue(":f_lenLoc",$inf->lenLoc,PDO::PARAM_INT);
		$cmd->bindValue(":f_lenSvr",$inf->lenLoc,PDO::PARAM_INT);
		$cmd->bindParam(":f_sizeLoc",$inf->sizeLoc);
		$cmd->bindValue(":f_blockCount",$inf->blockCount,PDO::PARAM_INT);
		$cmd->bindValue(":f_blockSize",$inf->blockSize,PDO::PARAM_INT);
		$cmd->bindParam(":f_blockPath",$inf->blockPath);
		$cmd->execute();
	}

	static function Clear()
	{
		$db = new DbHelper();
		$db->ExecuteNonQueryTxt("delete from up7_files;");
		$db->ExecuteNonQueryTxt("delete from up7_folders;");
	}

	/// <summary>
	/// 
	/// </summary>
	/// <param name="f_uid"></param>
	/// <param name="f_id">文件ID</param>
	function Complete($uid,$fid)
	{
		$db = new DbHelper();
		$cmd =& $db->GetCommand("update up7_files set f_lenSvr=f_lenLoc,f_perSvr='100%',f_complete=1 where f_id=:f_id and f_uid=:f_uid;");
		$cmd->bindParam(":f_id",$fid);
		$cmd->bindParam(":f_uid",$uid);
		$db->ExecuteNonQuery($cmd);
	}
	function remove($idSign)
	{
		$sql = "update up7_files set f_deleted=1 where f_idSign=:f_idSign";
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);
		
		$cmd->bindParam(":f_idSign", $idSign);
		$db->ExecuteNonQuery($cmd);
	}
}
?>