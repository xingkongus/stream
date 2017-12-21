-- phpMyAdmin SQL Dump
-- version 4.4.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2017-12-21 17:59:46
-- 服务器版本： 10.1.25-MariaDB-
-- PHP Version: 7.0.22-0ubuntu0.17.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stream`
--

-- --------------------------------------------------------

--
-- 表的结构 `stream_app`
--

CREATE TABLE IF NOT EXISTS `stream_app` (
  `id` int(11) NOT NULL COMMENT '自增id',
  `appname` varchar(16) COLLATE utf8_bin NOT NULL COMMENT '直播应用名',
  `title` varchar(32) COLLATE utf8_bin NOT NULL COMMENT '直播应用标题',
  `maintext` text COLLATE utf8_bin NOT NULL COMMENT '直播应用描述',
  `token` varchar(32) COLLATE utf8_bin NOT NULL COMMENT '推流令牌',
  `username` varchar(16) COLLATE utf8_bin NOT NULL COMMENT '所属用户'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='直播应用表';

--
-- 转存表中的数据 `stream_app`
--

INSERT INTO `stream_app` (`id`, `appname`, `title`, `maintext`, `token`, `username`) VALUES
(1, 'test', '测试', '哈哈哈', 'eba7d732a6978ba87a009f67cd16b7d1', 'hansin'),
(2, 'sad', 'asd', 'asd', '4c8ec4762b17eb58fceb00432229c6ef', 'hansin');

-- --------------------------------------------------------

--
-- 表的结构 `stream_app_group`
--

CREATE TABLE IF NOT EXISTS `stream_app_group` (
  `id` int(11) NOT NULL COMMENT '自增id',
  `apps` text COLLATE utf8_bin NOT NULL COMMENT '直播组',
  `username` tinytext COLLATE utf8_bin NOT NULL COMMENT '创建者',
  `groupname` varchar(16) COLLATE utf8_bin NOT NULL COMMENT '直播组唯一标识名',
  `grouptitle` varchar(32) COLLATE utf8_bin NOT NULL COMMENT '直播组标题',
  `groupmaintext` text COLLATE utf8_bin NOT NULL COMMENT '直播组描述'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- 转存表中的数据 `stream_app_group`
--

INSERT INTO `stream_app_group` (`id`, `apps`, `username`, `groupname`, `grouptitle`, `groupmaintext`) VALUES
(4, '["sad","test"]', 'hansin', 'ewe', 'hahah', 'xxxxx');

-- --------------------------------------------------------

--
-- 表的结构 `stream_rtmp_user`
--

CREATE TABLE IF NOT EXISTS `stream_rtmp_user` (
  `id` int(11) NOT NULL COMMENT '自增id',
  `username` varchar(16) COLLATE utf8_bin NOT NULL COMMENT '直播用户名',
  `password` varchar(32) COLLATE utf8_bin NOT NULL COMMENT '密码',
  `nickname` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '昵称',
  `utype` int(3) NOT NULL COMMENT '用户类型',
  `token` varchar(32) COLLATE utf8_bin NOT NULL COMMENT '临时token'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='RTMP用户表';

--
-- 转存表中的数据 `stream_rtmp_user`
--

INSERT INTO `stream_rtmp_user` (`id`, `username`, `password`, `nickname`, `utype`, `token`) VALUES
(3, 'hansin', '917928e28bdd7d249d7f77256dbebb8c', '绕韩信', 1, 'e9c3aa2c92a1700360b3b641f54b8a70');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `stream_app`
--
ALTER TABLE `stream_app`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stream_app_group`
--
ALTER TABLE `stream_app_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stream_rtmp_user`
--
ALTER TABLE `stream_rtmp_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `stream_app`
--
ALTER TABLE `stream_app`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `stream_app_group`
--
ALTER TABLE `stream_app_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `stream_rtmp_user`
--
ALTER TABLE `stream_rtmp_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
