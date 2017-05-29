-- phpMyAdmin SQL Dump
-- version 2.11.2.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2015 年 01 月 30 日 02:16
-- 服务器版本: 5.0.45
-- PHP 版本: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- 数据库: `httpuploader6`
--

-- --------------------------------------------------------

--
-- 表的结构 `up7_files`
--

CREATE TABLE `up7_files` (
  `f_idSign` char(36) NOT NULL,
  `f_pidSign` char(36) default '',
  `f_rootSign` char(36) default '',
  `f_fdTask` tinyint(1) default '0',
  `f_fdChild` tinyint(1) default '0',
  `f_uid` int(11) default '0',
  `f_nameLoc` varchar(255) default '',
  `f_nameSvr` varchar(255) default '',
  `f_pathLoc` varchar(255) default '',
  `f_pathSvr` varchar(255) default '',
  `f_pathRel` varchar(255) default '',
  `f_md5` varchar(40) default '',
  `f_lenLoc` bigint(19) default '0',
  `f_sizeLoc` varchar(15) default '0',
  `f_pos` bigint(19) default '0',
  `f_blockCount` int(11) default '1',
  `f_blockSize` int(11) default '0',
  `f_blockPath` varchar(1000) default '',
  `f_lenSvr` bigint(19) default '0',
  `f_perSvr` varchar(6) default '0%',
  `f_complete` tinyint(1) default '0',
  `f_time` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `f_deleted` tinyint(1) default '0',
  `f_sign` varchar(32) default '',
  PRIMARY KEY  (`f_idSign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
