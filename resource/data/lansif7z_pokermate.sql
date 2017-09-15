-- MySQL dump 10.14  Distrib 5.5.40-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: lansif7z_pokermate
-- ------------------------------------------------------
-- Server version	5.5.40-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `lansif7z_pokermate`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `lansif7z_pokermate` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `lansif7z_pokermate`;

--
-- Table structure for table `agent`
--

DROP TABLE IF EXISTS `agent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `agent_name` varchar(50) NOT NULL COMMENT '代理名字',
  `is_delete` tinyint(4) NOT NULL COMMENT '是否删除:1是0否',
  `create_time` int(11) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agent`
--

LOCK TABLES `agent` WRITE;
/*!40000 ALTER TABLE `agent` DISABLE KEYS */;
INSERT INTO `agent` VALUES (1,1,'李白',0,1504790304);
INSERT INTO `agent` VALUES (2,1,'南苏',0,1504791752);
/*!40000 ALTER TABLE `agent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `club`
--

DROP TABLE IF EXISTS `club`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `club` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `club_name` varchar(50) NOT NULL COMMENT '俱乐部名称',
  `club_id` int(11) NOT NULL COMMENT '俱乐部ID',
  `club_login_name` varchar(50) NOT NULL COMMENT '俱乐部登录账户',
  `club_login_password` varchar(50) NOT NULL COMMENT '俱乐部登录账户密码',
  `last_import_date` varchar(20) NOT NULL COMMENT '上一次成功导入excel日期',
  `is_delete` tinyint(4) NOT NULL COMMENT '是否删除:1是0否',
  `create_time` int(11) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `club`
--

LOCK TABLES `club` WRITE;
/*!40000 ALTER TABLE `club` DISABLE KEYS */;
INSERT INTO `club` VALUES (1,1,'888分舵',888520,'jay','123456','',1,1504667409);
INSERT INTO `club` VALUES (2,1,'888国际',2767536,'13612646126','123456789','',0,1504766433);
/*!40000 ALTER TABLE `club` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `excel_file`
--

DROP TABLE IF EXISTS `excel_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `excel_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `club_id` int(11) NOT NULL COMMENT '俱乐部ID',
  `room_id` int(11) NOT NULL COMMENT '房间ID',
  `type` int(11) NOT NULL COMMENT '类型(1:全部2:SNG3:普通4:奥马哈5:6+)',
  `path` varchar(200) NOT NULL COMMENT '文件保存路径',
  `download_time` int(11) NOT NULL COMMENT '下载成功时间',
  `import_time` int(11) NOT NULL COMMENT '导入成功时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `excel_file`
--

LOCK TABLES `excel_file` WRITE;
/*!40000 ALTER TABLE `excel_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `excel_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fencheng_setting`
--

DROP TABLE IF EXISTS `fencheng_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fencheng_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `zhuozi_jibie` varchar(50) NOT NULL COMMENT '桌子级别',
  `yingfan` decimal(10,2) NOT NULL COMMENT '赢返',
  `shufan` decimal(10,2) NOT NULL COMMENT '输返',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fencheng_setting`
--

LOCK TABLES `fencheng_setting` WRITE;
/*!40000 ALTER TABLE `fencheng_setting` DISABLE KEYS */;
INSERT INTO `fencheng_setting` VALUES (1,1,'1/2',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (2,1,'2/4',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (3,1,'5/10',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (4,1,'10/20',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (5,1,'20/40',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (6,1,'25/50',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (7,1,'50/100',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (8,1,'100/200',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (9,1,'200/400',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (10,1,'300/600',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (11,1,'1000/2000',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (12,2,'1/2',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (13,2,'2/4',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (14,2,'5/10',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (15,2,'10/20',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (16,2,'20/40',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (17,2,'25/50',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (18,2,'50/100',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (19,2,'100/200',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (20,2,'200/400',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (21,2,'300/600',0.00,0.00);
INSERT INTO `fencheng_setting` VALUES (22,2,'1000/2000',0.00,0.00);
/*!40000 ALTER TABLE `fencheng_setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `import_data`
--

DROP TABLE IF EXISTS `import_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `import_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `paiju_type` varchar(50) NOT NULL COMMENT '牌局类型',
  `paiju_name` varchar(50) NOT NULL COMMENT '牌局名',
  `paiju_creater` varchar(50) NOT NULL COMMENT '建局者昵称',
  `mangzhu` varchar(50) NOT NULL COMMENT '盲注',
  `paizuo` varchar(50) NOT NULL COMMENT '牌桌',
  `paiju_duration` varchar(50) NOT NULL COMMENT '牌局时长',
  `zongshoushu` int(11) NOT NULL COMMENT '总手数',
  `player_id` int(11) NOT NULL COMMENT '玩家ID',
  `player_name` varchar(50) NOT NULL COMMENT '玩家昵称',
  `club_id` int(11) NOT NULL COMMENT '俱乐部ID',
  `club_name` varchar(50) NOT NULL COMMENT '俱乐部',
  `mairu` int(11) NOT NULL COMMENT '买入',
  `daicu` int(11) NOT NULL COMMENT '带出',
  `baoxian_mairu` int(11) NOT NULL COMMENT '保险买入',
  `baoxian_shouru` int(11) NOT NULL COMMENT '保险收入',
  `baoxian_heji` int(11) NOT NULL COMMENT '保险合计',
  `club_baoxian` int(11) NOT NULL COMMENT '俱乐部保险',
  `baoxian` int(11) NOT NULL COMMENT '保险',
  `zhanji` int(11) NOT NULL COMMENT '战绩',
  `end_time_format` varchar(50) NOT NULL COMMENT '结束时间',
  `end_time` int(11) NOT NULL COMMENT '结束时间戳',
  `create_time` int(11) NOT NULL COMMENT '时间',
  `original_zhanji` varchar(50) NOT NULL COMMENT '原始战绩',
  `paiju_id` int(11) NOT NULL COMMENT '牌局ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `status` tinyint(4) NOT NULL COMMENT '状态:0未结算1已结算',
  `jiesuan_value` int(11) NOT NULL COMMENT '结算值',
  `choushui_value` int(11) NOT NULL COMMENT '抽水值：正数为赢抽,负数为输返',
  `agent_is_clean` tinyint(4) NOT NULL COMMENT '代理是否清账：1是0否',
  `club_is_clean` tinyint(4) NOT NULL COMMENT '俱乐部是否清账：1是0否',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `import_data`
--

LOCK TABLES `import_data` WRITE;
/*!40000 ALTER TABLE `import_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `import_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `keren_benjin`
--

DROP TABLE IF EXISTS `keren_benjin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `keren_benjin` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `keren_bianhao` int(11) NOT NULL COMMENT '客人编号',
  `benjin` int(11) NOT NULL COMMENT '本金',
  `ying_chou` decimal(10,2) NOT NULL COMMENT '赢抽点数',
  `shu_fan` decimal(10,2) NOT NULL COMMENT '输返点数',
  `agent_id` int(11) NOT NULL COMMENT '代理人ID',
  `remark` varchar(500) NOT NULL COMMENT '备注',
  `is_delete` tinyint(4) NOT NULL COMMENT '是否删除:1是0否',
  `create_time` int(11) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `keren_benjin`
--

LOCK TABLES `keren_benjin` WRITE;
/*!40000 ALTER TABLE `keren_benjin` DISABLE KEYS */;
/*!40000 ALTER TABLE `keren_benjin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lianmeng`
--

DROP TABLE IF EXISTS `lianmeng`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lianmeng` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `name` varchar(50) NOT NULL COMMENT '联盟名称',
  `qianzhang` int(11) NOT NULL COMMENT '联盟欠账',
  `duizhangfangfa` int(11) NOT NULL COMMENT '对账方法（1：0.975 2：无水账单）',
  `paiju_fee` int(11) NOT NULL COMMENT '上缴桌费',
  `baoxian_choucheng` int(11) NOT NULL COMMENT '保险抽成',
  `is_delete` tinyint(4) NOT NULL COMMENT '是否删除:1是0否',
  `create_time` int(11) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lianmeng`
--

LOCK TABLES `lianmeng` WRITE;
/*!40000 ALTER TABLE `lianmeng` DISABLE KEYS */;
INSERT INTO `lianmeng` VALUES (1,1,'默认联盟',1107,1,4,4,0,1504964957);
INSERT INTO `lianmeng` VALUES (2,1,'BOO大',0,1,2,5,0,1504966087);
INSERT INTO `lianmeng` VALUES (3,1,'77联盟',0,1,0,0,0,1504966515);
INSERT INTO `lianmeng` VALUES (4,1,'五特区',0,1,0,0,0,1504966761);
INSERT INTO `lianmeng` VALUES (5,2,'默认联盟',0,1,0,0,0,1505453449);
/*!40000 ALTER TABLE `lianmeng` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lianmeng_club`
--

DROP TABLE IF EXISTS `lianmeng_club`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lianmeng_club` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `lianmeng_id` int(11) NOT NULL COMMENT '联盟ID',
  `club_id` int(11) NOT NULL COMMENT '俱乐部ID',
  `club_name` varchar(50) NOT NULL COMMENT '俱乐部名称',
  `qianzhang` int(11) NOT NULL COMMENT '俱乐部欠账（旧账）',
  `duizhangfangfa` int(11) NOT NULL COMMENT '对账方法（1：0.975 2：无水账单）',
  `paiju_fee` int(11) NOT NULL COMMENT '上缴桌费',
  `baoxian_choucheng` int(11) NOT NULL COMMENT '保险抽成',
  `is_delete` tinyint(4) NOT NULL COMMENT '是否删除:1是0否',
  `create_time` int(11) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lianmeng_club`
--

LOCK TABLES `lianmeng_club` WRITE;
/*!40000 ALTER TABLE `lianmeng_club` DISABLE KEYS */;
INSERT INTO `lianmeng_club` VALUES (1,1,1,2767536,'888国际',-405,1,0,0,0,1505278078);
INSERT INTO `lianmeng_club` VALUES (2,1,1,1798364,'🌟村长家🌟',403,1,0,0,0,1505278305);
INSERT INTO `lianmeng_club` VALUES (3,1,1,568568,'小肥羊俱乐部',-438,1,0,0,0,1505278377);
INSERT INTO `lianmeng_club` VALUES (4,1,1,555558,'打死为止小班💎',760,1,0,0,0,1505278395);
INSERT INTO `lianmeng_club` VALUES (5,1,1,288888,'金字塔俱乐部',422,1,0,0,0,1505278414);
INSERT INTO `lianmeng_club` VALUES (6,1,1,227733,'水上公园轻级别',542,1,0,0,0,1505278430);
INSERT INTO `lianmeng_club` VALUES (7,1,1,21065196,'海洋馆',-14,1,0,0,0,1505278461);
INSERT INTO `lianmeng_club` VALUES (8,1,1,21340895,'渔船',-106,1,0,0,0,1505278474);
INSERT INTO `lianmeng_club` VALUES (9,1,1,21955639,'新西兰27poker',529,1,0,0,0,1505278491);
INSERT INTO `lianmeng_club` VALUES (10,1,2,2767536,'888国际',0,1,0,0,0,1505278078);
INSERT INTO `lianmeng_club` VALUES (11,1,2,1798364,'🌟村长家🌟',0,1,0,0,0,1505278305);
INSERT INTO `lianmeng_club` VALUES (12,1,2,568568,'小肥羊俱乐部',0,1,0,0,0,1505278377);
INSERT INTO `lianmeng_club` VALUES (13,1,2,555558,'打死为止小班💎',0,1,0,0,0,1505278395);
INSERT INTO `lianmeng_club` VALUES (14,1,2,288888,'金字塔俱乐部',0,1,0,0,0,1505278414);
INSERT INTO `lianmeng_club` VALUES (15,1,2,227733,'水上公园轻级别',0,1,0,0,0,1505278430);
INSERT INTO `lianmeng_club` VALUES (16,1,2,21065196,'海洋馆',0,1,0,0,0,1505278461);
INSERT INTO `lianmeng_club` VALUES (17,1,2,21340895,'渔船',0,1,0,0,0,1505278474);
INSERT INTO `lianmeng_club` VALUES (18,1,2,21955639,'新西兰27poker',0,1,0,0,0,1505278491);
/*!40000 ALTER TABLE `lianmeng_club` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `money_out_put_type`
--

DROP TABLE IF EXISTS `money_out_put_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `money_out_put_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `out_put_type` varchar(50) NOT NULL COMMENT '支出方式',
  `money` int(11) NOT NULL COMMENT '金额',
  `is_delete` tinyint(4) NOT NULL COMMENT '是否删除:1是0否',
  `create_time` int(11) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `money_out_put_type`
--

LOCK TABLES `money_out_put_type` WRITE;
/*!40000 ALTER TABLE `money_out_put_type` DISABLE KEYS */;
INSERT INTO `money_out_put_type` VALUES (1,1,'伙食',0,0,1504689955);
INSERT INTO `money_out_put_type` VALUES (2,1,'电费',0,0,1504689973);
/*!40000 ALTER TABLE `money_out_put_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `money_type`
--

DROP TABLE IF EXISTS `money_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `money_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `pay_type` varchar(50) NOT NULL COMMENT '支付方式',
  `money` int(11) NOT NULL COMMENT '金额',
  `is_delete` tinyint(4) NOT NULL COMMENT '是否删除:1是0否',
  `create_time` int(11) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `money_type`
--

LOCK TABLES `money_type` WRITE;
/*!40000 ALTER TABLE `money_type` DISABLE KEYS */;
INSERT INTO `money_type` VALUES (1,1,'支付宝',683,0,1504680294);
INSERT INTO `money_type` VALUES (2,1,'微信',0,0,1504682724);
INSERT INTO `money_type` VALUES (3,1,'银行卡',0,0,1504688074);
/*!40000 ALTER TABLE `money_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paiju`
--

DROP TABLE IF EXISTS `paiju`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paiju` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `paiju_name` varchar(50) NOT NULL COMMENT '牌局名',
  `end_time` int(11) NOT NULL COMMENT '结束时间戳',
  `status` tinyint(4) NOT NULL COMMENT '牌局状态:0未结算1已结算2已交班',
  `lianmeng_id` int(11) NOT NULL COMMENT '联盟ID',
  `is_clean` tinyint(4) NOT NULL COMMENT '联盟是否已清账：1是0否',
  `create_time` int(11) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paiju`
--

LOCK TABLES `paiju` WRITE;
/*!40000 ALTER TABLE `paiju` DISABLE KEYS */;
/*!40000 ALTER TABLE `paiju` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player`
--

DROP TABLE IF EXISTS `player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `player` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `keren_bianhao` int(11) NOT NULL COMMENT '客人编号',
  `player_id` int(11) NOT NULL COMMENT '玩家游戏ID',
  `player_name` varchar(50) NOT NULL COMMENT '玩家游戏名字',
  `is_delete` tinyint(4) NOT NULL COMMENT '是否删除:1是0否',
  `create_time` int(11) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player`
--

LOCK TABLES `player` WRITE;
/*!40000 ALTER TABLE `player` DISABLE KEYS */;
/*!40000 ALTER TABLE `player` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `type` int(11) NOT NULL COMMENT '用户类型：0普通用户1后台用户2微信用户3QQ用户4微博用户5支付宝用户',
  `openid` varchar(100) NOT NULL COMMENT '第三方用户id',
  `name` varchar(50) NOT NULL COMMENT '用户名',
  `login_name` varchar(50) NOT NULL COMMENT '登录账号名',
  `mobile` varchar(11) NOT NULL COMMENT '手机',
  `email` varchar(50) NOT NULL COMMENT '邮箱',
  `password` varchar(100) NOT NULL COMMENT '密码',
  `profile_path` varchar(200) NOT NULL COMMENT '头像',
  `sex` tinyint(1) NOT NULL COMMENT '性别：1男2女',
  `qibu_choushui` int(11) NOT NULL COMMENT '起步抽水',
  `choushui_shuanfa` tinyint(4) NOT NULL COMMENT '抽水算法:1四舍五入2余数抹零',
  `choushui_ajust_value` int(11) NOT NULL COMMENT '总抽水修正值：总抽水=总抽水修正值+公式算出来的总抽水',
  `baoxian_ajust_value` int(11) NOT NULL COMMENT '总保险修正值：总保险=总保险修正值+公式算出来的总保险',
  `agent_fencheng_ajust_value` int(11) NOT NULL COMMENT '代理总分成修正值：代理总分成=代理总分成修正值+公式算出来的代理总分成',
  `vip_level` int(11) NOT NULL COMMENT 'vip等级:0,1,2,3,4,5,6,7,8,9,10(0为非会员)',
  `vip_expire_time` int(11) NOT NULL COMMENT 'vip过期时间',
  `is_forbidden` tinyint(4) NOT NULL COMMENT '是否禁用:1是0否',
  `create_time` int(11) NOT NULL COMMENT '注册时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,1,'','admin','admin','','','123456','',1,20,2,0,0,0,7,1535654426,0,1499654426);
INSERT INTO `user` VALUES (2,0,'','gsw','gsw','','','123456','',0,10,2,0,0,0,1,1507996800,0,1505452947);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-09-15 16:48:34
