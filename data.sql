-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2019 �?07 �?25 �?15:02
-- 服务器版本: 5.5.53
-- PHP 版本: 7.2.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `demo_t1zf`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user` varchar(11) NOT NULL,
  `pass` varchar(16) NOT NULL,
  `apikey` varchar(16) NOT NULL,
  `money` float(8,4) NOT NULL,
  `token` varchar(32) NOT NULL,
  `filemax` bigint(20) NOT NULL DEFAULT '52428800',
  `max_user` int(11) NOT NULL DEFAULT '1000',
  `max_mail` int(11) NOT NULL DEFAULT '10000',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `admin_info`
--

CREATE TABLE IF NOT EXISTS `admin_info` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `reg_time` datetime NOT NULL,
  `reg_ip` varchar(20) NOT NULL,
  `reg_city` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `apk`
--

CREATE TABLE IF NOT EXISTS `apk` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `title` varchar(20) NOT NULL,
  `fileid` varchar(20) NOT NULL,
  `apkname` varchar(10) NOT NULL,
  `apkbb` varchar(10) NOT NULL,
  `message` text NOT NULL,
  `is_downsee` int(11) NOT NULL COMMENT '0显示下载数量',
  `icon` varchar(100) NOT NULL,
  `img1` varchar(100) NOT NULL,
  `img2` varchar(100) NOT NULL,
  `img3` varchar(100) NOT NULL,
  `img4` varchar(100) NOT NULL,
  `url` varchar(10) NOT NULL COMMENT '自定义后缀下载地址',
  `page` int(11) NOT NULL COMMENT '页面地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `download_log`
--

CREATE TABLE IF NOT EXISTS `download_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fileid` bigint(20) NOT NULL,
  `istext` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `file`
--

CREATE TABLE IF NOT EXISTS `file` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `filename` varchar(100) NOT NULL,
  `filesize` bigint(20) NOT NULL,
  `addtime` datetime NOT NULL,
  `md5` varchar(32) NOT NULL,
  `download` bigint(20) NOT NULL,
  `ispay` int(1) NOT NULL,
  `msg` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `log_admin_login`
--

CREATE TABLE IF NOT EXISTS `log_admin_login` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `addtime` datetime NOT NULL,
  `ip` varchar(20) NOT NULL,
  `city` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `log_admin_money`
--

CREATE TABLE IF NOT EXISTS `log_admin_money` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `money` float(6,4) NOT NULL,
  `addtime` datetime NOT NULL,
  `message` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `log_admin_sms`
--

CREATE TABLE IF NOT EXISTS `log_admin_sms` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `addtime` datetime NOT NULL,
  `ip` varchar(20) NOT NULL,
  `tel` varchar(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `log_admin_sys`
--

CREATE TABLE IF NOT EXISTS `log_admin_sys` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `addtime` datetime NOT NULL,
  `dowhat` varchar(50) NOT NULL COMMENT '做什么',
  `message` varchar(200) NOT NULL COMMENT '说明',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `pay_config`
--

CREATE TABLE IF NOT EXISTS `pay_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `e_api` varchar(100) NOT NULL,
  `e_pid` varchar(10) NOT NULL,
  `e_key` varchar(50) NOT NULL,
  `m_pid` varchar(10) NOT NULL,
  `m_key` varchar(50) NOT NULL,
  `qqpay` varchar(10) NOT NULL DEFAULT 'off',
  `alipay` varchar(10) NOT NULL DEFAULT 'off',
  `wxpay` varchar(10) NOT NULL DEFAULT 'off',
  `return_mb` int(11) NOT NULL COMMENT '页面返回通知的模板',
  `sysmsg` int(11) NOT NULL,
  `ismail` int(1) NOT NULL COMMENT '1开启',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `pay_goods`
--

CREATE TABLE IF NOT EXISTS `pay_goods` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '商品ID',
  `admin_id` bigint(20) NOT NULL,
  `title` text NOT NULL,
  `money` float(6,2) NOT NULL,
  `addtime` datetime NOT NULL,
  `addmsg` varchar(200) NOT NULL,
  `dowhat` varchar(20) NOT NULL,
  `doconfig` text NOT NULL,
  `havenum` bigint(20) NOT NULL COMMENT '当前库存',
  `sell` bigint(20) NOT NULL COMMENT '已出售数量',
  `zt` int(1) NOT NULL COMMENT '0正常1下架',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `pay_goods_km`
--

CREATE TABLE IF NOT EXISTS `pay_goods_km` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `addtime` datetime NOT NULL,
  `goodsid` varchar(20) NOT NULL,
  `km` varchar(200) NOT NULL,
  `issell` int(1) NOT NULL,
  `orderid` varchar(20) NOT NULL,
  `admin_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `pay_order`
--

CREATE TABLE IF NOT EXISTS `pay_order` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `orderid` varchar(30) NOT NULL,
  `goodsid` bigint(20) NOT NULL,
  `goodsval` text NOT NULL,
  `paytype` varchar(10) NOT NULL,
  `paysys` varchar(10) NOT NULL,
  `money` float(6,2) NOT NULL,
  `add_time` datetime NOT NULL,
  `add_msg` varchar(300) NOT NULL,
  `add_ip` varchar(15) NOT NULL,
  `ispay` int(1) NOT NULL,
  `pay_time` datetime NOT NULL,
  `isdo` int(1) NOT NULL,
  `do_time` datetime NOT NULL,
  `do_msg` varchar(300) NOT NULL,
  `iscz` int(1) NOT NULL,
  `ismoney` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `pay_order_mail`
--

CREATE TABLE IF NOT EXISTS `pay_order_mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `orderid` varchar(20) NOT NULL,
  `zt` int(1) NOT NULL COMMENT '1已执行',
  PRIMARY KEY (`id`),
  UNIQUE KEY `orderid` (`orderid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=214 ;

-- --------------------------------------------------------

--
-- 表的结构 `smtp_config`
--

CREATE TABLE IF NOT EXISTS `smtp_config` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `smtp` varchar(30) NOT NULL,
  `port` int(11) NOT NULL,
  `isssl` int(11) NOT NULL,
  `user` varchar(25) NOT NULL,
  `pass` varchar(20) NOT NULL,
  `name` varchar(10) NOT NULL,
  `adminmail` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `smtp_log`
--

CREATE TABLE IF NOT EXISTS `smtp_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `add_time` datetime NOT NULL,
  `add_ip` varchar(15) NOT NULL,
  `add_title` varchar(20) NOT NULL,
  `add_text` varchar(300) NOT NULL,
  `add_who` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `telboom`
--

CREATE TABLE IF NOT EXISTS `telboom` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `tel` varchar(11) NOT NULL,
  `num` int(11) NOT NULL,
  `i` int(11) NOT NULL,
  `addtime` datetime NOT NULL,
  `addnum` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `telboom_bmd`
--

CREATE TABLE IF NOT EXISTS `telboom_bmd` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `tel` varchar(11) NOT NULL,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `textlist`
--

CREATE TABLE IF NOT EXISTS `textlist` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `msg` varchar(10) NOT NULL,
  `textinfo` text NOT NULL,
  `addtime` datetime NOT NULL,
  `see` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `user` varchar(20) NOT NULL,
  `pass` varchar(32) NOT NULL,
  `imei` varchar(20) NOT NULL,
  `jf` int(11) NOT NULL,
  `money` float(9,2) NOT NULL,
  `vip` datetime NOT NULL,
  `custom` text NOT NULL COMMENT '自定义内容',
  `reg_time` datetime NOT NULL,
  `reg_ip` varchar(15) NOT NULL,
  `zt` int(11) NOT NULL COMMENT '0正常(1封号',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `users_choujiang`
--

CREATE TABLE IF NOT EXISTS `users_choujiang` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `msg` varchar(20) NOT NULL,
  `addtime` datetime NOT NULL,
  `cjtime` datetime NOT NULL,
  `admin_id` bigint(20) NOT NULL,
  `user` varchar(20) NOT NULL,
  `dowhat` varchar(5) NOT NULL,
  `dovalue` varchar(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `users_config`
--

CREATE TABLE IF NOT EXISTS `users_config` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `reg_jf` int(11) NOT NULL,
  `reg_vip` int(11) NOT NULL,
  `reg_money` float(6,2) NOT NULL,
  `reg_onlyimei` int(11) NOT NULL COMMENT '1开启imei绑定',
  `qd_open` int(11) NOT NULL COMMENT '0打开1关闭',
  `qd_jf` int(11) NOT NULL,
  `qd_vip` int(11) NOT NULL,
  `qd_money` float(6,2) NOT NULL,
  `qdlist_open` int(11) NOT NULL COMMENT '0关闭1开启',
  `edu_custom` int(11) NOT NULL COMMENT '0关闭1开启',
  `reg_open` int(11) NOT NULL COMMENT '1关闭0开启',
  `login_imei` int(11) NOT NULL COMMENT '1开启imei绑定登陆',
  `mtj_open` int(11) NOT NULL COMMENT '1关闭余额换积分',
  `mtj` int(6) NOT NULL DEFAULT '1' COMMENT '1余额换多少积分',
  `mtv_open` int(11) NOT NULL COMMENT '1关闭余额换vip',
  `mtv` float(6,2) NOT NULL COMMENT '1个月VIP需要余额多少',
  `cj_open` int(11) NOT NULL COMMENT '1关闭抽奖',
  `cj_needwhat` varchar(3) NOT NULL DEFAULT '积分' COMMENT '抽奖需要消耗多少',
  `cj_needvalue` varchar(6) NOT NULL DEFAULT '0' COMMENT '消耗多少',
  `cj_isvip` int(11) NOT NULL COMMENT '1抽奖需要是会员',
  `cj_daynum` int(11) NOT NULL DEFAULT '1' COMMENT '每天可抽几次',
  `yq_what` varchar(3) NOT NULL DEFAULT '积分' COMMENT '邀请赠送',
  `yq_value` varchar(6) NOT NULL DEFAULT '0' COMMENT '赠送额度',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `users_config`
--

INSERT INTO `users_config` (`id`, `admin_id`, `reg_jf`, `reg_vip`, `reg_money`, `reg_onlyimei`, `qd_open`, `qd_jf`, `qd_vip`, `qd_money`, `qdlist_open`, `edu_custom`, `reg_open`, `login_imei`, `mtj_open`, `mtj`, `mtv_open`, `mtv`, `cj_open`, `cj_needwhat`, `cj_needvalue`, `cj_isvip`, `cj_daynum`, `yq_what`, `yq_value`) VALUES
(1, 1, 0, 0, 0.00, 0, 0, 0, 0, 0.00, 0, 0, 0, 0, 0, 1, 0, 0.00, 0, '积分', '0', 0, 1, '积分', '0'),
(2, 2, 0, 0, 0.00, 0, 0, 0, 0, 0.00, 0, 0, 0, 0, 0, 1, 0, 0.00, 0, '积分', '0', 0, 1, '积分', '0');

-- --------------------------------------------------------

--
-- 表的结构 `users_log_all`
--

CREATE TABLE IF NOT EXISTS `users_log_all` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `user` varchar(10) NOT NULL,
  `dowhat` varchar(10) NOT NULL,
  `addtime` datetime NOT NULL,
  `adddate` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `users_log_custom`
--

CREATE TABLE IF NOT EXISTS `users_log_custom` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `user` varchar(20) NOT NULL,
  `custom` text NOT NULL,
  `msg` varchar(30) NOT NULL,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `users_log_jf`
--

CREATE TABLE IF NOT EXISTS `users_log_jf` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `user` varchar(20) NOT NULL,
  `num` int(11) NOT NULL,
  `msg` varchar(30) NOT NULL,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `users_log_money`
--

CREATE TABLE IF NOT EXISTS `users_log_money` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `user` varchar(20) NOT NULL,
  `num` float(6,2) NOT NULL,
  `msg` varchar(30) NOT NULL,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `users_log_vip`
--

CREATE TABLE IF NOT EXISTS `users_log_vip` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) NOT NULL,
  `user` varchar(20) NOT NULL,
  `num` int(11) NOT NULL,
  `msg` varchar(30) NOT NULL,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
