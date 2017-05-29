-- phpMyAdmin SQL Dump
-- version 2.11.2.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2015 年 01 月 30 日 02:36
-- 服务器版本: 5.0.45
-- PHP 版本: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- 数据库: `httpuploader6`
--

-- --------------------------------------------------------

--
-- 表的结构 `up7_folders`
--

CREATE TABLE up7_folders (
   fd_sign 			char(36) NOT NULL
  ,fd_name 			varchar(50) default ''
  ,fd_pidSign 		char(32) default ''
  ,fd_uid 			int(11) default '0'
  ,fd_length 		bigint(19) default '0'
  ,fd_size 			varchar(50) default '0'
  ,fd_pathLoc 		varchar(255) default ''
  ,fd_pathSvr 		varchar(255) default ''
  ,fd_folders 		int(11) default '0'
  ,fd_files 		int(11) default '0'
  ,fd_filesComplete int(11) default '0'
  ,fd_complete 		tinyint(1) default '0'
  ,fd_delete 		tinyint(1) default '0'
  ,fd_json 			varchar(20000) default ''
  ,timeUpload 		timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
  ,fd_rootSign		char(36) default ''		 /*--根级uuid*/
  ,fd_pathRel		varchar(255) default ''  /*--相对路径。基于顶级节点。root\\child\\self*/
  ,PRIMARY KEY  (fd_sign)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;