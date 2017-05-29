drop procedure if exists fd_remove;
DELIMITER $$
/* =============================================
-- Author:		zysoft
-- Create date: 2016-08-04
-- Description:	批量查询相同MD5的文件
-- =============================================
*/
CREATE PROCEDURE fd_remove(
	 in idSign varchar(36)
	,in uid int
)
BEGIN
	update up7_files set f_deleted=1 where f_idSign=idSign and f_uid=uid;
	update up7_files set f_deleted=1 where f_rootSign=idSign and f_uid=uid;
	update up7_folders set fd_delete=1 where f_idSign=idSign and fd_uid=uid;
END$$
DELIMITER;/*--5.7.9版本MySQL必须加这一句，否则包含多条SQL语句的存储过程无法创建成功*/