<?php
class BlockPathBuilder
{
	/**
	 * 取块路径
	 * 格式：
	 * 	upload/guid/1.part
	 * @param unknown $idSign
	 * @param unknown $blockIndex
	 * @param unknown $pathSvr
	 * @return mixed
	 */
	function part($idSign,$blockIndex,$pathSvr)
	{
		$parent = dirname($pathSvr);
		//upload/guid
		$pathSvr = PathTool::combin($parent, $idSign);
		//upload/guid/1.part
		$pathSvr = PathTool::combin($pathSvr, "$blockIndex.part");
		$pathSvr = str_replace("\\", "/", $pathSvr);
		return $pathSvr;
	}
	
	/**
	 * 文件块根路径
	 *   d:/webapps/files/年/月/日/file-guid/
	 * @param unknown $idSign
	 * @param unknown $pathSvr
	 * @return Ambigous <unknown, string>
	 */
	function root($idSign,$pathSvr)
	{
		$parent = dirname($pathSvr);
		$pathSvr = PathTool::combin($parent, $idSign);
		$pathSvr = str_replace("\\", "/", $pathSvr);
		return $pathSvr;
	}
	
	/**
	 * 子文件块路径
	 *   d:/webapps/files/年/月/日/folder/file-guid/
	 * @param unknown $child
	 * @param unknown $blockIndex
	 * @param unknown $fd
	 */
	function rootFD($child,$blockIndex,$fd)
	{
		$pos = strrpos($child->pathRel, "\\");
		//在根目录中
		if(is_bool($pos) || !$pos)
		{
			return PathTool::combin($fd->pathSvr, $child->idSign);			
		}//在子目录中
		else 
		{
			//取相对路径，soft/dev/php
			$rel = substr($child->pathRel,0,$pos);
			$path = PathTool::combin($fd->pathSvr, $rel);
			$path = PathTool::combin($path, $child->idSign);
			return $path;
		}
	}
}
?>