<?php
/*
	说明：
		1.在调用此函数前不能有任何输出操作。比如 echo print
		
	更新记录：
		2012-04-03 创建
		2014-08-11 更新数据库操作代码。 
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

	/// <summary>
	/// 
	/// </summary>
	/// <param name="f_uid"></param>
	/// <param name="f_id">文件ID</param>
	function fd_complete($idSvr)
	{
		$db = new DbHelper();
		$cmd =& $db->GetCommand("update up7_files set f_perSvr='100%',f_lenSvr=f_lenLoc,f_complete=1 where f_id=:f_id;");
		$cmd->bindParam(":f_id",$idSvr);
		$db->ExecuteNonQuery($cmd);
	}
	
    function fd_fileProcess($uid, $f_id, $f_pos, $lenSvr, $perSvr, $fd_idSvr, $fd_lenSvr,$fd_perSvr,$complete)
    {
    	$this->f_process($uid, $f_id, $f_pos, $lenSvr, $perSvr,$complete);
    	$this->fd_process($uid, $fd_idSvr, $fd_lenSvr,$fd_perSvr);
    }
    
    function fd_process($uid,$fd_idSvr,$fd_lenSvr,$perSvr)
    {
        $sql = "call fd_process(:uid,:idSvr,:lenSvr,:per)";
        $db = $this->db;
        $cmd =& $db->GetCommand($sql);     

		$cmd->bindParam(":uid", $uid);
		$cmd->bindParam(":idSvr", $fd_idSvr);
		$cmd->bindParam(":lenSvr", $fd_lenSvr);
		$cmd->bindParam(":per", $perSvr);

		$db->ExecuteNonQuery($cmd);
		return true;
	}

	/// <summary>
	/// 更新上传进度
	/// </summary>
	///<param name="f_uid">用户ID</param>
	///<param name="f_id">文件ID</param>
	///<param name="f_pos">文件位置，大小可能超过2G，所以需要使用long保存</param>
	///<param name="f_lenSvr">已上传长度，文件大小可能超过2G，所以需要使用long保存</param>
	///<param name="f_perSvr">已上传百分比</param>
	function f_process($f_uid,$f_id,$f_pos,$f_lenSvr,$f_perSvr,$complete)
	{
		//$sql = "update up7_files set f_pos=?,f_lenSvr=?,f_perSvr=? where f_uid=? and f_id=?";
		$sql = "call f_process(:pos,:len,:per,:uid,:id,:cmp)";//使用存储过程
		$db = &$this->db;
		$cmd =& $db->GetCommand($sql);
		
		$cmd->bindParam(":pos",$f_pos);
		$cmd->bindParam(":len",$f_lenSvr);
		$cmd->bindParam(":per",$f_perSvr);
		$cmd->bindParam(":uid",$f_uid);
		$cmd->bindParam(":id",$f_id);
		$cmd->bindParam(":cmp",$complete,PDO::PARAM_BOOL);//fix(2016-05-26)

		$db->ExecuteNonQuery($cmd);
		return true;
	}

	/// <summary>
	/// 上传完成。将所有相同MD5文件进度都设为100%
	/// </summary>
	function UploadComplete($md5)
	{
		$sql = "update up7_files set f_lenSvr=f_lenLoc,f_perSvr='100%',f_complete=1 where f_md5=:f_md5";
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);
		
		$cmd->bindParam(":f_md5", $md5);
		$db->ExecuteNonQuery($cmd);
	}

	/// <summary>
	/// 检查相同MD5文件是否有已经上传完的文件
	/// </summary>
	/// <param name="md5"></param>
	function HasCompleteFile($md5)
	{
		//为空
		if (empty($md5)) return false;

		$sql = "select f_id from up7_files where f_complete=1 and f_md5=:f_md5";
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);

		$cmd->bindParam(":f_md5", $md5);
		$ret = $db->ExecuteScalar($cmd);

		return empty($ret);
	}

	/// <summary>
	/// 删除一条数据，并不真正删除，只更新删除标识。
	/// </summary>
	/// <param name="f_uid"></param>
	/// <param name="f_id"></param>
	function Delete($f_uid,$f_id)
	{
		$sql = "update up7_files set f_deleted=1 where f_uid=:f_uid and f_id=:f_id";
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);

		$cmd->bindParam(":f_uid", $f_uid);
		$cmd->bindParam(":f_id", $f_id);
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

	/// <summary>
	/// 根据根文件夹ID获取未上传完成的文件列表，并转换成JSON格式。
	/// 说明：
	///		1.此函数会自动对文件路径进行转码
	/// </summary>
	/// <param name="fidRoot"></param>
	/// <returns></returns>
	function GetUnCompletesJson($fidRoot)
	{
		$sql = "select ";
		$sql = $sql . "f_nameLoc";
		$sql = $sql . ",f_pathLoc";
		$sql = $sql . ",f_lenLoc";
		$sql = $sql . ",f_sizeLoc";
		$sql = $sql . ",f_md5";
		$sql = $sql . ",f_pidRoot";
		$sql = $sql . ",f_pid";
		$sql = $sql . " from up7_files where f_pidRoot=:f_pidRoot;";		

		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);
		$cmd->bindParam(":f_pidRoot", $fidRoot);
		$list = $db->ExecuteDataSet($cmd);
		
		$arrFiles = array();
		foreach($list as $row)
		{
			$fi = new FileInf();
			$fi->nameLoc = $row["f_nameLoc"];
			$fi->pathLoc = $row["f_pathLoc"];
			$fi->pathLoc = urlencode($fi->pathLoc);
			$fi->pathLoc = str_replace("+","%20",$fi->pathLoc);
			$fi->lenLoc = $row["f_lenLoc"];
			$fi->sizeLoc = $row["f_sizeLoc"];
			$fi->md5 = $row["f_md5"];
			$fi->pidRoot = intval($row["f_pidRoot"]);
			$fi->pidSvr = intval($row["f_pid"]);
			array_push($arrFiles,json_encode($fi));
		}
		return json_encode($arrFiles);
	}

	/// <summary>
	/// 获取未上传完的文件列表
	/// </summary>
	/// <param name="fidRoot"></param>
	/// <param name="files"></param>
	function GetUnCompletesArr($fidRoot,&$files)
	{
		$sql = "select ";
		$sql = $sql . "f_id";
		$sql = $sql . ",f_nameLoc";
		$sql = $sql . ",f_pathLoc";
		$sql = $sql . ",f_pathSvr";
		$sql = $sql . ",f_lenLoc";
		$sql = $sql . ",f_sizeLoc";
		$sql = $sql . ",f_md5";
		$sql = $sql . ",f_pidRoot";
		$sql = $sql . ",f_pid";
		$sql = $sql . ",f_lenSvr";
		$sql = $sql . " from up7_files where f_pidRoot=:f_pidRoot and f_complete=0;";

		$db = new DbHelper();	
		$cmd =& $db->GetCommand($sql);
		$db->ExecuteNonQueryConTxt("set names utf8");
		$cmd->bindParam(":f_pidRoot", $fidRoot);
		
		$list = $db->ExecuteDataSet($cmd);
		foreach($list as $row)
		{
			$fi				= new FileInf();
			$fi->idSvr		= intval($row["f_id"]);
			$fi->nameLoc	= $row["f_nameLoc"];
			$fi->pathLoc	= $row["f_pathLoc"];
			$fi->pathSvr	= $row["f_pathSvr"];
			$fi->lenLoc		= $row["f_lenLoc"];
			$fi->sizeLoc	= $row["f_sizeLoc"];
			$fi->md5		= $row["f_md5"];
			$fi->pidRoot	= intval($row["f_pidRoot"]);
			$fi->pidSvr		= intval($row["f_pid"]);
			$fi->lenSvr 	= $row["f_lenSvr"];
			array_push($files,$fi);
		}
	}
}
?>