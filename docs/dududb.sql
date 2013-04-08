-- phpMyAdmin SQL Dump
-- version 3.5.0-rc1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2013 年 04 月 07 日 15:33
-- 服务器版本: 5.0.51b-community-nt-log
-- PHP 版本: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `dududb_local`
--

-- --------------------------------------------------------

--
-- 表的结构 `dudu_passenger 乘客表`
--

CREATE TABLE IF NOT EXISTS `dudu_passenger` (
  `pid` int(11) unsigned NOT NULL auto_increment COMMENT '乘客ID',
  `username` char(11) NOT NULL COMMENT '用户名',
  `passwd` char(32) default NULL COMMENT '密码',
  `email` varchar(64) default NULL COMMENT '邮箱',
  `nickname` varchar(32) NOT NULL COMMENT '称呼',
  `sex` tinyint(1) NOT NULL COMMENT '性别',
  `reg_time` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT '注册时间',
  `reg_ip` varchar(39) NOT NULL COMMENT '注册IP',
  `last_login_time` timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT '最后一次登录时间',
  `last_login_ip` varchar(39) NOT NULL COMMENT '最后一次登录IP',
  `last_login_lng` varchar(11) default NULL COMMENT '最后一次登录经度',
  `last_login_lat` varchar(11) default NULL COMMENT '最后一次登录纬度',
  `status` tinyint(1) unsigned NOT NULL default '1' COMMENT '状态',
  `more_info` varchar(1024) default NULL COMMENT '更多存json信息',
  PRIMARY KEY  (`pid`),
  KEY `username` (`username`)
) ENGINE=innodb DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 表的结构 `dudu_driver 司机表`
--

CREATE TABLE IF NOT EXISTS `dudu_driver` (
  `did` int(11) unsigned NOT NULL auto_increment COMMENT '司机ID',
  `username` char(11) NOT NULL COMMENT '用户名',
  `passwd` char(32) default NULL COMMENT '密码',
  `email` varchar(64) default NULL COMMENT '邮箱',
  `nickname` varchar(32) NOT NULL COMMENT '称呼',
  `sex` tinyint(1) NOT NULL COMMENT '性别',
  `car_number` varchar(8) NOT NULL COMMENT '车牌号',
  `tax_company_id` smallint(6) unsigned NOT NULL COMMENT '出租车公司ID',
  `driver_number` varchar(10) NOT NULL COMMENT '准驾驶证号',
  `rec_username` char(11) NOT NULL COMMENT '推荐人手机号',
  `reg_time` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT '注册时间',
  `reg_ip` varchar(39) NOT NULL COMMENT '注册IP',
  `last_login_time` timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT '最后一次登录时间',
  `last_login_ip` varchar(39) NOT NULL COMMENT '最后一次登录IP',
  `last_login_lng` varchar(11) default NULL COMMENT '最后一次登录经度',
  `last_login_lat` varchar(11) default NULL COMMENT '最后一次登录纬度',
  `status` tinyint(1) unsigned NOT NULL default '1' COMMENT '状态',
  `more_info` varchar(1024) default NULL COMMENT '更多存json信息',
  PRIMARY KEY  (`did`),
  KEY `username` (`username`),
  KEY `car_number` (`car_number`),
  KEY `tax_company_id` (`tax_company_id`),
  KEY `driver_number` (`driver_number`),
  KEY `rec_username` (`rec_username`)
) ENGINE=innodb DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 表的结构 `dudu_order_normal 普通订单表`
--

CREATE TABLE IF NOT EXISTS `dudu_order_normal` (
  `order_id` int(11) unsigned NOT NULL COMMENT '订单id',
  `pid` int(11) NOT NULL COMMENT '乘客ID',
  `did` int(11) NOT NULL COMMENT '司机ID',
  `passenger_lng` varchar(11) NOT NULL COMMENT '乘客经度',
  `passenger_lat` varchar(11) NOT NULL COMMENT '乘客纬度',
  `driver_lng` varchar(11) NOT NULL COMMENT '司机经度',
  `driver_lat` varchar(11) NOT NULL COMMENT '司机纬度',
  `status` tinyint(1) NOT NULL COMMENT '订单状态',
  `request_time` timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT '乘客请求时间',
  `reply_time` timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT '司机应答时间',
  `start_point` varchar(64) NOT NULL COMMENT '起始地',
  `end_point` varchar(64) NOT NULL COMMENT '目的地',
  `driver_leave_time` timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT '司机出发时间',
  `passenger_rided_time` timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT '乘客乘座时间',
  `ride_lng` varchar(11) NOT NULL COMMENT '乘坐地点经度',
  `ride_lat` varchar(11) NOT NULL COMMENT '乘坐地点纬度',
  `evaluate` tinyint(1) unsigned NOT NULL default '0' COMMENT '评价状态',
  `voice_url` varchar(64) default NULL COMMENT '语音url',
  PRIMARY KEY  (`order_id`),
  KEY `pid` (`pid`),
  KEY `did` (`did`),
  KEY `status` (`status`),
  KEY `request_time` (`request_time`),
  KEY `evaluate` (`evaluate`)
) ENGINE=innodb DEFAULT CHARSET=utf8 ;

--
-- 表的结构 `dudu_order_reserve 预约订单表`
--

CREATE TABLE IF NOT EXISTS `dudu_order_reserve` (
  `order_id` int(11) unsigned NOT NULL COMMENT '订单id',
  `pid` int(11) NOT NULL COMMENT '乘客ID',
  `did` int(11) NOT NULL COMMENT '司机ID',
  `passenger_lng` varchar(11) NOT NULL COMMENT '乘客经度',
  `passenger_lat` varchar(11) NOT NULL COMMENT '乘客纬度',
  `driver_lng` varchar(11) NOT NULL COMMENT '司机经度',
  `driver_lat` varchar(11) NOT NULL COMMENT '司机纬度',
  `status` tinyint(1) NOT NULL COMMENT '订单状态',
  `request_time` timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT '乘客请求时间',
  `reply_time` timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT '司机应答时间',
  `start_point` varchar(64) NOT NULL COMMENT '起始地',
  `end_point` varchar(64) NOT NULL COMMENT '目的地',
  `driver_leave_time` timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT '司机出发时间',
  `passenger_rided_time` timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT '乘客乘座时间',
  `ride_lng` varchar(11) NOT NULL COMMENT '乘坐地点经度',
  `ride_lat` varchar(11) NOT NULL COMMENT '乘坐地点纬度',
  `evaluate` tinyint(1) unsigned NOT NULL default '0' COMMENT '评价状态',
  `voice_url` varchar(64) default NULL COMMENT '语音url',
  PRIMARY KEY  (`order_id`),
  KEY `pid` (`pid`),
  KEY `did` (`did`),
  KEY `status` (`status`),
  KEY `request_time` (`request_time`),
  KEY `evaluate` (`evaluate`)
) ENGINE=innodb DEFAULT CHARSET=utf8 ;

--
-- 表的结构 `driver_order 司机订单统计表`
--

CREATE TABLE IF NOT EXISTS `driver_order` (
  `pid` int(11) unsigned NOT NULL COMMENT '乘客ID',
  `all_num` mediumint(8) unsigned NOT NULL COMMENT '所有订单数',
  `success_num` mediumint(8) unsigned default NULL COMMENT '成功数',
  `fail_num` mediumint(6) unsigned default NULL COMMENT '失败数',
  `broke_num` mediumint(6) unsigned NOT NULL COMMENT '爽约数',
  `m_num` mediumint(8) unsigned NOT NULL COMMENT '当月订单数',
  `m_success_num` mediumint(8) unsigned default NULL COMMENT '当月成功数',
  `m_fail_num` mediumint(6) unsigned default NULL COMMENT '当月失败数',
  `m_broke_num` mediumint(6) unsigned NOT NULL COMMENT '当月爽约数',
  PRIMARY KEY  (`pid`),
  KEY `all_num` (`all_num`),
  KEY `m_num` (`m_num`)
) ENGINE=innodb DEFAULT CHARSET=utf8 ;

--
-- 表的结构 `passenger_order 乘客订单统计表`
--

CREATE TABLE IF NOT EXISTS `passenger_order` (
  `pid` int(11) unsigned NOT NULL COMMENT '乘客ID',
  `all_num` mediumint(8) unsigned NOT NULL COMMENT '所有订单数',
  `success_num` mediumint(8) unsigned default NULL COMMENT '成功数',
  `fail_num` mediumint(6) unsigned default NULL COMMENT '失败数',
  `broke_num` mediumint(6) unsigned NOT NULL COMMENT '爽约数',
  PRIMARY KEY  (`pid`)
) ENGINE=innodb DEFAULT CHARSET=utf8 ;

--
-- 表的结构 `order_path 订单路径表`
--

CREATE TABLE IF NOT EXISTS `order_path` (
  `order_id` int(11) unsigned NOT NULL COMMENT '订单id',
  `path_info` text  default NULL COMMENT '路径经纬度信息',
  PRIMARY KEY  (`order_id`)
) ENGINE=innodb DEFAULT CHARSET=utf8 ;


--
-- 表的结构 `order_evaluate 订单评价表`
--

CREATE TABLE IF NOT EXISTS `order_evaluate` (
  `order_id` int(11) unsigned NOT NULL COMMENT '订单id',
  `evaluate_type` tinyint(1) NOT NULL COMMENT '评价类型',
  `content` varchar(1024) NOT NULL COMMENT '评价内容_JSON',
  PRIMARY KEY  (`order_id`)
) ENGINE=innodb DEFAULT CHARSET=utf8 ;

--
-- 表的结构 `tax_company 出租车公司`
--

CREATE TABLE IF NOT EXISTS `tax_company` (
  `tax_company_id` smallint(6) unsigned NOT NULL auto_increment COMMENT '出租车公司ID',
  `tax_company_name` varchar(32) NOT NULL COMMENT '出租车公司简称',
  `tax_company_full_name` varchar(64) NOT NULL COMMENT '出租车公司全称',
  `city_id` smallint(6) unsigned NOT NULL COMMENT '城市ID',
  PRIMARY KEY  (`tax_company_id`)
) ENGINE=innodb DEFAULT CHARSET=utf8 ;

--
-- 表的结构 `city 城市`
--

CREATE TABLE IF NOT EXISTS `city` (
  `city_id` smallint(6) unsigned NOT NULL auto_increment COMMENT '城市ID',
  `city_name` varchar(32) NOT NULL COMMENT '城市名',
  `province_id` smallint(6) NOT NULL COMMENT '省份ID',
  `status` tinyint(1) unsigned NOT NULL default '0' COMMENT '评价状态',
  PRIMARY KEY  (`city_id`)
) ENGINE=innodb DEFAULT CHARSET=utf8 ;


--
-- 表的结构 `province 省份`
--

CREATE TABLE IF NOT EXISTS `province` (
  `province_id` smallint(6) unsigned NOT NULL auto_increment COMMENT '省份ID',
  `province_name` varchar(32) NOT NULL COMMENT '省份名',
  PRIMARY KEY  (`province_id`)
) ENGINE=innodb DEFAULT CHARSET=utf8 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
