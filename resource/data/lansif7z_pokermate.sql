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
INSERT INTO `club` VALUES (1,1,'888分舵',888520,'jay','123456',0,1504667409);
INSERT INTO `club` VALUES (2,1,'888国际',2767536,'13612646126','123456789',0,1504766433);
/*!40000 ALTER TABLE `club` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;
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
  `zongshoushu` varchar(50) NOT NULL COMMENT '总手数',
  `player_id` varchar(50) NOT NULL COMMENT '玩家ID',
  `player_name` varchar(50) NOT NULL COMMENT '玩家昵称',
  `club_id` varchar(50) NOT NULL COMMENT '俱乐部ID',
  `club_name` varchar(50) NOT NULL COMMENT '俱乐部',
  `mairu` varchar(50) NOT NULL COMMENT '买入',
  `daicu` varchar(50) NOT NULL COMMENT '带出',
  `baoxian_mairu` varchar(50) NOT NULL COMMENT '保险买入',
  `baoxian_shouru` varchar(50) NOT NULL COMMENT '保险收入',
  `baoxian_heji` varchar(50) NOT NULL COMMENT '保险合计',
  `club_baoxian` varchar(50) NOT NULL COMMENT '俱乐部保险',
  `baoxian` varchar(50) NOT NULL COMMENT '保险',
  `zhanji` varchar(50) NOT NULL COMMENT '战绩',
  `end_time_format` varchar(50) NOT NULL COMMENT '结束时间',
  `end_time` int(11) NOT NULL COMMENT '结束时间戳',
  `create_time` int(11) NOT NULL COMMENT '时间',
  `original_zhanji` varchar(50) NOT NULL COMMENT '原始战绩',
  `paiju_id` int(11) NOT NULL COMMENT '牌局ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `status` tinyint(4) NOT NULL COMMENT '状态:0未结算1已结算',
  `jiesuan_value` int(11) NOT NULL COMMENT '结算值',
  `choushui_value` int(11) NOT NULL COMMENT '抽水值：正数为赢抽,负数为输返',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `import_data`
--

LOCK TABLES `import_data` WRITE;
/*!40000 ALTER TABLE `import_data` DISABLE KEYS */;
INSERT INTO `import_data` VALUES (1,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','735304085','妖股','21955639','新西兰27poker','800','657','0','0','0','0','295','-143','2017-08-12 07:30:58',1502494258,1505034049,'-143',1,1,0,0,0);
INSERT INTO `import_data` VALUES (2,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','481124234','DAB621','21340895','渔船','400','1337','0','0','0','0','295','937','2017-08-12 07:30:58',1502494258,1505034049,'937',1,1,0,0,0);
INSERT INTO `import_data` VALUES (3,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','441053099','PersonZ','21340895','渔船','400','383','0','0','0','0','295','-17','2017-08-12 07:30:58',1502494258,1505034049,'-17',1,1,0,0,0);
INSERT INTO `import_data` VALUES (4,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','796020048','神相李布衣','21065196','海洋馆','400','400','0','0','0','8','295','0','2017-08-12 07:30:58',1502494258,1505034049,'0',1,1,0,0,0);
INSERT INTO `import_data` VALUES (5,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','1272637032','阿玮（TW）','21065196','海洋馆','400','79','8','0','-8','8','295','-321','2017-08-12 07:30:58',1502494258,1505034049,'-321',1,1,0,0,0);
INSERT INTO `import_data` VALUES (6,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','1292548547','厚先生','2767536','888国际','1000','1447','0','0','0','0','295','447','2017-08-12 07:30:58',1502494258,1505034049,'447',1,1,1,447,0);
INSERT INTO `import_data` VALUES (7,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','94816688','冰泉水','2767536','888国际','400','580','0','0','0','0','295','180','2017-08-12 07:30:58',1502494258,1505034049,'180',1,1,1,171,9);
INSERT INTO `import_data` VALUES (8,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','1490946566','买牌买到底','2767536','888国际','600','507','0','0','0','0','295','-93','2017-08-12 07:30:58',1502494258,1505034049,'-93',1,1,1,-93,0);
INSERT INTO `import_data` VALUES (9,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','1232996650','高尔基','1798364','🌟村长家🌟','400','400','0','0','0','14','295','0','2017-08-12 07:30:58',1502494258,1505034049,'0',1,1,0,0,0);
INSERT INTO `import_data` VALUES (10,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','1355487424','哈气','1798364','🌟村长家🌟','400','0','14','0','-14','14','295','-400','2017-08-12 07:30:58',1502494258,1505034049,'-400',1,1,0,0,0);
INSERT INTO `import_data` VALUES (11,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','1239328724','releeyk0','568568','小肥羊俱乐部','800','1272','273','0','-273','273','295','472','2017-08-12 07:30:58',1502494258,1505034049,'472',1,1,0,0,0);
INSERT INTO `import_data` VALUES (12,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','1191529058','價值下注','568568','小肥羊俱乐部','600','984','0','0','0','273','295','384','2017-08-12 07:30:58',1502494258,1505034049,'384',1,1,0,0,0);
INSERT INTO `import_data` VALUES (13,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','1130923485','AAFish','568568','小肥羊俱乐部','800','991','0','0','0','273','295','191','2017-08-12 07:30:58',1502494258,1505034049,'191',1,1,0,0,0);
INSERT INTO `import_data` VALUES (14,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','1355192654','MU Jheng','568568','小肥羊俱乐部','1200','1270','0','0','0','273','295','70','2017-08-12 07:30:58',1502494258,1505034049,'70',1,1,0,0,0);
INSERT INTO `import_data` VALUES (15,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','1608005878','踩过介','568568','小肥羊俱乐部','600','600','0','0','0','273','295','0','2017-08-12 07:30:58',1502494258,1505034049,'0',1,1,0,0,0);
INSERT INTO `import_data` VALUES (16,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','1626414532','人數字','568568','小肥羊俱乐部','400','0','0','0','0','273','295','-400','2017-08-12 07:30:58',1502494258,1505034049,'-400',1,1,0,0,0);
INSERT INTO `import_data` VALUES (17,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','1413166582','稀粥炒饭','555558','打死为止小班💎','1000','913','0','0','0','0','295','-87','2017-08-12 07:30:58',1502494258,1505034049,'-87',1,1,0,0,0);
INSERT INTO `import_data` VALUES (18,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','54381787','深海小醜🐟','555558','打死为止小班💎','400','0','0','0','0','0','295','-400','2017-08-12 07:30:58',1502494258,1505034049,'-400',1,1,0,0,0);
INSERT INTO `import_data` VALUES (19,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','1015655431','美少女月野兔','555558','打死为止小班💎','800','217','0','0','0','0','295','-583','2017-08-12 07:30:58',1502494258,1505034049,'-583',1,1,0,0,0);
INSERT INTO `import_data` VALUES (20,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','1032824372','狼狼的刷','288888','金字塔俱乐部','800','0','0','0','0','0','295','-800','2017-08-12 07:30:58',1502494258,1505034049,'-800',1,1,0,0,0);
INSERT INTO `import_data` VALUES (21,'普通保险局','2/4🍎1181A','海洋馆导游','2/4','8','3.0','242','1157888537','老虎一号','227733','水上公园轻级别','400','666','0','0','0','0','295','266','2017-08-12 07:30:58',1502494258,1505034049,'266',1,1,0,0,0);
INSERT INTO `import_data` VALUES (22,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','1605248194','To7Ny','23524010','💰元寶小級別💰','400','400','0','0','0','0','88','0','2017-08-12 09:14:35',1502500475,1505034064,'0',2,1,0,0,0);
INSERT INTO `import_data` VALUES (23,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','617523719','谁与争锋丶','23524010','💰元寶小級別💰','400','290','0','0','0','0','88','-110','2017-08-12 09:14:35',1502500475,1505034064,'-110',2,1,1,-110,0);
INSERT INTO `import_data` VALUES (24,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','434905276','钱多多$','21955639','新西兰27poker','400','0','0','0','0','0','88','-400','2017-08-12 09:14:35',1502500475,1505034064,'-400',2,1,0,0,0);
INSERT INTO `import_data` VALUES (25,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','481124234','DAB621','21340895','渔船','400','1000','47','0','-47','47','88','600','2017-08-12 09:14:35',1502500475,1505034064,'600',2,1,1,300,300);
INSERT INTO `import_data` VALUES (26,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','951364414','凡a凡','21340895','渔船','400','425','0','0','0','47','88','25','2017-08-12 09:14:35',1502500475,1505034064,'25',2,1,0,0,0);
INSERT INTO `import_data` VALUES (27,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','1178956061','徐小白','21340895','渔船','600','600','0','0','0','47','88','0','2017-08-12 09:14:35',1502500475,1505034064,'0',2,1,0,0,0);
INSERT INTO `import_data` VALUES (28,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','660072563','最后一次机会','21340895','渔船','400','113','0','0','0','47','88','-287','2017-08-12 09:14:35',1502500475,1505034064,'-287',2,1,0,0,0);
INSERT INTO `import_data` VALUES (29,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','215455274','巍仔','21340895','渔船','1200','143','0','0','0','47','88','-1057','2017-08-12 09:14:35',1502500475,1505034064,'-1057',2,1,0,0,0);
INSERT INTO `import_data` VALUES (30,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','499460465','统统跟我走','21065196','海洋馆','600','1369','26','0','-26','26','88','769','2017-08-12 09:14:35',1502500475,1505034064,'769',2,1,1,769,0);
INSERT INTO `import_data` VALUES (31,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','906451795','fish5555','21065196','海洋馆','400','400','0','0','0','26','88','0','2017-08-12 09:14:35',1502500475,1505034064,'0',2,1,0,0,0);
INSERT INTO `import_data` VALUES (32,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','1082812126','收大米','21065196','海洋馆','400','0','0','0','0','26','88','-400','2017-08-12 09:14:35',1502500475,1505034064,'-400',2,1,0,0,0);
INSERT INTO `import_data` VALUES (33,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','111249150','牛老总','20607927','珍宝岛俱乐部','400','500','0','0','0','0','88','100','2017-08-12 09:14:35',1502500475,1505034064,'100',2,1,0,0,0);
INSERT INTO `import_data` VALUES (34,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','1110184598','土豆少年','2767536','888国际','1200','1924','0','0','0','15','88','724','2017-08-12 09:14:35',1502500475,1505034064,'724',2,1,1,506,218);
INSERT INTO `import_data` VALUES (35,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','94816688','冰泉水','2767536','888国际','400','507','15','0','-15','15','88','107','2017-08-12 09:14:35',1502500475,1505034064,'107',2,1,1,101,6);
INSERT INTO `import_data` VALUES (36,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','1292490098','伟大的厨师','2767536','888国际','1000','673','0','0','0','15','88','-327','2017-08-12 09:14:35',1502500475,1505034064,'-327',2,1,1,-327,0);
INSERT INTO `import_data` VALUES (37,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','1239328724','releeyk0','568568','小肥羊俱乐部','400','653','0','0','0','0','88','253','2017-08-12 09:14:35',1502500475,1505034064,'253',2,1,0,0,0);
INSERT INTO `import_data` VALUES (38,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','1317503305','Bill Hsu','568568','小肥羊俱乐部','400','400','0','0','0','0','88','0','2017-08-12 09:14:35',1502500475,1505034064,'0',2,1,0,0,0);
INSERT INTO `import_data` VALUES (39,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','1276274697','魚類123','568568','小肥羊俱乐部','400','298','0','0','0','0','88','-102','2017-08-12 09:14:35',1502500475,1505034064,'-102',2,1,0,0,0);
INSERT INTO `import_data` VALUES (40,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','1032824372','狼狼的刷','288888','金字塔俱乐部','400','337','0','0','0','0','88','-63','2017-08-12 09:14:35',1502500475,1505034064,'-63',2,1,0,0,0);
INSERT INTO `import_data` VALUES (41,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','1617701919','曉票r','227733','水上公园轻级别','600','1567','0','0','0','0','88','967','2017-08-12 09:14:35',1502500475,1505034064,'967',2,1,0,0,0);
INSERT INTO `import_data` VALUES (42,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','1337048195','T0319450','227733','水上公园轻级别','400','0','0','0','0','0','88','-400','2017-08-12 09:14:35',1502500475,1505034064,'-400',2,1,0,0,0);
INSERT INTO `import_data` VALUES (43,'普通保险局','2/4🍎1183A','海洋馆导游','2/4','8','3.0','166','1567908496','markmak','227733','水上公园轻级别','800','313','0','0','0','0','88','-487','2017-08-12 09:14:35',1502500475,1505034064,'-487',2,1,0,0,0);
INSERT INTO `import_data` VALUES (44,'普通保险局','2/4🍎1201A','海洋馆导游','2/4','8','3.0','196','441053099','PersonZ','21340895','渔船','1000','1832','327','0','-327','327','489','832','2017-08-12 10:37:29',1502505449,1505034077,'832',3,1,0,0,0);
INSERT INTO `import_data` VALUES (45,'普通保险局','2/4🍎1201A','海洋馆导游','2/4','8','3.0','196','513587202','Chrischan5784','21340895','渔船','800','649','0','0','0','327','489','-151','2017-08-12 10:37:29',1502505449,1505034077,'-151',3,1,0,0,0);
INSERT INTO `import_data` VALUES (46,'普通保险局','2/4🍎1201A','海洋馆导游','2/4','8','3.0','196','15447489','谭咏麟','21340895','渔船','400','0','0','0','0','327','489','-400','2017-08-12 10:37:29',1502505449,1505034077,'-400',3,1,0,0,0);
INSERT INTO `import_data` VALUES (47,'普通保险局','2/4🍎1201A','海洋馆导游','2/4','8','3.0','196','1515633112','鸟人带翅膀','2767536','888国际','400','629','32','0','-32','32','489','229','2017-08-12 10:37:29',1502505449,1505034077,'229',3,1,1,183,46);
INSERT INTO `import_data` VALUES (48,'普通保险局','2/4🍎1201A','海洋馆导游','2/4','8','3.0','196','1110230534','捕鱼小船','2767536','888国际','800','834','0','0','0','32','489','34','2017-08-12 10:37:29',1502505449,1505034077,'34',3,1,1,27,7);
INSERT INTO `import_data` VALUES (49,'普通保险局','2/4🍎1201A','海洋馆导游','2/4','8','3.0','196','1490968705','盲头操作','2767536','888国际','1600','763','0','0','0','32','489','-837','2017-08-12 10:37:29',1502505449,1505034077,'-837',3,1,1,-669,-168);
INSERT INTO `import_data` VALUES (50,'普通保险局','2/4🍎1201A','海洋馆导游','2/4','8','3.0','196','1380480643','德普新手','568568','小肥羊俱乐部','400','402','0','0','0','0','489','2','2017-08-12 10:37:29',1502505449,1505034077,'2',3,1,0,0,0);
INSERT INTO `import_data` VALUES (51,'普通保险局','2/4🍎1201A','海洋馆导游','2/4','8','3.0','196','1452511206','万年树','568568','小肥羊俱乐部','1600','1454','0','0','0','0','489','-146','2017-08-12 10:37:29',1502505449,1505034077,'-146',3,1,0,0,0);
INSERT INTO `import_data` VALUES (52,'普通保险局','2/4🍎1201A','海洋馆导游','2/4','8','3.0','196','54381787','深海小醜🐟','555558','打死为止小班💎','400','690','0','0','0','0','489','290','2017-08-12 10:37:29',1502505449,1505034077,'290',3,1,0,0,0);
INSERT INTO `import_data` VALUES (53,'普通保险局','2/4🍎1201A','海洋馆导游','2/4','8','3.0','196','752687236','佳佳！','288888','金字塔俱乐部','1000','1560','130','0','-130','130','489','560','2017-08-12 10:37:29',1502505449,1505034077,'560',3,1,0,0,0);
INSERT INTO `import_data` VALUES (54,'普通保险局','2/4🍎1201A','海洋馆导游','2/4','8','3.0','196','1157888537','老虎一号','227733','水上公园轻级别','400','820','0','0','0','0','489','420','2017-08-12 10:37:29',1502505449,1505034077,'420',3,1,0,0,0);
INSERT INTO `import_data` VALUES (55,'普通保险局','2/4🍎1201A','海洋馆导游','2/4','8','3.0','196','1102740911','Thousand aster','227733','水上公园轻级别','400','606','0','0','0','0','489','206','2017-08-12 10:37:29',1502505449,1505034077,'206',3,1,0,0,0);
INSERT INTO `import_data` VALUES (56,'普通保险局','2/4🍎1201A','海洋馆导游','2/4','8','3.0','196','1386834399','王志文','227733','水上公园轻级别','800','676','0','0','0','0','489','-124','2017-08-12 10:37:29',1502505449,1505034077,'-124',3,1,0,0,0);
INSERT INTO `import_data` VALUES (57,'普通保险局','2/4🍎1201A','海洋馆导游','2/4','8','3.0','196','922368156','大良风之子','227733','水上公园轻级别','800','315','0','0','0','0','489','-485','2017-08-12 10:37:29',1502505449,1505034077,'-485',3,1,0,0,0);
INSERT INTO `import_data` VALUES (58,'普通保险局','2/4🍎1201A','海洋馆导游','2/4','8','3.0','196','1478927499','小魔鬼KK','227733','水上公园轻级别','2200','1281','0','0','0','0','489','-919','2017-08-12 10:37:29',1502505449,1505034077,'-919',3,1,0,0,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `keren_benjin`
--

LOCK TABLES `keren_benjin` WRITE;
/*!40000 ALTER TABLE `keren_benjin` DISABLE KEYS */;
INSERT INTO `keren_benjin` VALUES (1,1,1,30,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (2,1,2,300,50.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (3,1,3,0,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (4,1,4,0,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (5,1,5,0,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (6,1,6,447,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (7,1,7,443,5.00,5.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (8,1,8,-93,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (9,1,9,0,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (10,1,10,0,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (11,1,11,0,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (12,1,12,0,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (13,1,13,0,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (14,1,14,0,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (15,1,15,0,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (16,1,16,0,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (17,1,17,0,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (18,1,18,0,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (19,1,19,0,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (20,1,20,0,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (21,1,21,0,0.00,0.00,0,'',0,1505034049);
INSERT INTO `keren_benjin` VALUES (22,1,22,0,0.00,0.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (23,1,23,-550,5.00,5.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (24,1,24,0,0.00,0.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (25,1,25,0,0.00,0.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (26,1,26,0,0.00,0.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (27,1,27,0,0.00,0.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (28,1,28,0,0.00,0.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (29,1,29,769,0.00,0.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (30,1,30,0,0.00,0.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (31,1,31,0,0.00,0.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (32,1,32,0,0.00,0.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (33,1,33,1230,30.00,0.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (34,1,34,-327,0.00,0.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (35,1,35,0,0.00,0.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (36,1,36,0,0.00,0.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (37,1,37,0,0.00,0.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (38,1,38,0,0.00,0.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (39,1,39,0,0.00,0.00,0,'',0,1505034064);
INSERT INTO `keren_benjin` VALUES (40,1,40,0,0.00,0.00,0,'',0,1505034077);
INSERT INTO `keren_benjin` VALUES (41,1,41,0,0.00,0.00,0,'',0,1505034077);
INSERT INTO `keren_benjin` VALUES (42,1,42,183,20.00,20.00,0,'',0,1505034077);
INSERT INTO `keren_benjin` VALUES (43,1,43,27,20.00,20.00,0,'',0,1505034077);
INSERT INTO `keren_benjin` VALUES (44,1,44,-669,20.00,20.00,0,'',0,1505034077);
INSERT INTO `keren_benjin` VALUES (45,1,45,0,0.00,0.00,0,'',0,1505034077);
INSERT INTO `keren_benjin` VALUES (46,1,46,0,0.00,0.00,0,'',0,1505034077);
INSERT INTO `keren_benjin` VALUES (47,1,47,0,0.00,0.00,0,'',0,1505034077);
INSERT INTO `keren_benjin` VALUES (48,1,48,0,0.00,0.00,0,'',0,1505034077);
INSERT INTO `keren_benjin` VALUES (49,1,49,0,0.00,0.00,0,'',0,1505034077);
INSERT INTO `keren_benjin` VALUES (50,1,50,0,0.00,0.00,0,'',0,1505034077);
INSERT INTO `keren_benjin` VALUES (51,1,51,0,0.00,0.00,0,'',0,1505034077);
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
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
/*!40000 ALTER TABLE `lianmeng` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paiju`
--

LOCK TABLES `paiju` WRITE;
/*!40000 ALTER TABLE `paiju` DISABLE KEYS */;
INSERT INTO `paiju` VALUES (1,1,'2/4🍎1181A',1502494258,2,1,1,1505034049);
INSERT INTO `paiju` VALUES (2,1,'2/4🍎1183A',1502500475,2,1,1,1505034064);
INSERT INTO `paiju` VALUES (3,1,'2/4🍎1201A',1502505449,2,1,1,1505034077);
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
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player`
--

LOCK TABLES `player` WRITE;
/*!40000 ALTER TABLE `player` DISABLE KEYS */;
INSERT INTO `player` VALUES (1,1,1,735304085,'妖股',0,1505034049);
INSERT INTO `player` VALUES (2,1,2,481124234,'DAB621',0,1505034049);
INSERT INTO `player` VALUES (3,1,3,441053099,'PersonZ',0,1505034049);
INSERT INTO `player` VALUES (4,1,4,796020048,'神相李布衣',0,1505034049);
INSERT INTO `player` VALUES (5,1,5,1272637032,'阿玮（TW）',0,1505034049);
INSERT INTO `player` VALUES (6,1,6,1292548547,'厚先生',0,1505034049);
INSERT INTO `player` VALUES (7,1,7,94816688,'冰泉水',0,1505034049);
INSERT INTO `player` VALUES (8,1,8,1490946566,'买牌买到底',0,1505034049);
INSERT INTO `player` VALUES (9,1,9,1232996650,'高尔基',0,1505034049);
INSERT INTO `player` VALUES (10,1,10,1355487424,'哈气',0,1505034049);
INSERT INTO `player` VALUES (11,1,11,1239328724,'releeyk0',0,1505034049);
INSERT INTO `player` VALUES (12,1,12,1191529058,'價值下注',0,1505034049);
INSERT INTO `player` VALUES (13,1,13,1130923485,'AAFish',0,1505034049);
INSERT INTO `player` VALUES (14,1,14,1355192654,'MU Jheng',0,1505034049);
INSERT INTO `player` VALUES (15,1,15,1608005878,'踩过介',0,1505034049);
INSERT INTO `player` VALUES (16,1,16,1626414532,'人數字',0,1505034049);
INSERT INTO `player` VALUES (17,1,17,1413166582,'稀粥炒饭',0,1505034049);
INSERT INTO `player` VALUES (18,1,18,54381787,'深海小醜🐟',0,1505034049);
INSERT INTO `player` VALUES (19,1,19,1015655431,'美少女月野兔',0,1505034049);
INSERT INTO `player` VALUES (20,1,20,1032824372,'狼狼的刷',0,1505034049);
INSERT INTO `player` VALUES (21,1,21,1157888537,'老虎一号',0,1505034049);
INSERT INTO `player` VALUES (22,1,22,1605248194,'To7Ny',0,1505034064);
INSERT INTO `player` VALUES (23,1,23,617523719,'谁与争锋丶',0,1505034064);
INSERT INTO `player` VALUES (24,1,24,434905276,'钱多多$',0,1505034064);
INSERT INTO `player` VALUES (25,1,25,951364414,'凡a凡',0,1505034064);
INSERT INTO `player` VALUES (26,1,26,1178956061,'徐小白',0,1505034064);
INSERT INTO `player` VALUES (27,1,27,660072563,'最后一次机会',0,1505034064);
INSERT INTO `player` VALUES (28,1,28,215455274,'巍仔',0,1505034064);
INSERT INTO `player` VALUES (29,1,29,499460465,'统统跟我走',0,1505034064);
INSERT INTO `player` VALUES (30,1,30,906451795,'fish5555',0,1505034064);
INSERT INTO `player` VALUES (31,1,31,1082812126,'收大米',0,1505034064);
INSERT INTO `player` VALUES (32,1,32,111249150,'牛老总',0,1505034064);
INSERT INTO `player` VALUES (33,1,33,1110184598,'土豆少年',0,1505034064);
INSERT INTO `player` VALUES (34,1,34,1292490098,'伟大的厨师',0,1505034064);
INSERT INTO `player` VALUES (35,1,35,1317503305,'Bill Hsu',0,1505034064);
INSERT INTO `player` VALUES (36,1,36,1276274697,'魚類123',0,1505034064);
INSERT INTO `player` VALUES (37,1,37,1617701919,'曉票r',0,1505034064);
INSERT INTO `player` VALUES (38,1,38,1337048195,'T0319450',0,1505034064);
INSERT INTO `player` VALUES (39,1,39,1567908496,'markmak',0,1505034064);
INSERT INTO `player` VALUES (40,1,40,513587202,'Chrischan5784',0,1505034077);
INSERT INTO `player` VALUES (41,1,41,15447489,'谭咏麟',0,1505034077);
INSERT INTO `player` VALUES (42,1,42,1515633112,'鸟人带翅膀',0,1505034077);
INSERT INTO `player` VALUES (43,1,43,1110230534,'捕鱼小船',0,1505034077);
INSERT INTO `player` VALUES (44,1,44,1490968705,'盲头操作',0,1505034077);
INSERT INTO `player` VALUES (45,1,45,1380480643,'德普新手',0,1505034077);
INSERT INTO `player` VALUES (46,1,46,1452511206,'万年树',0,1505034077);
INSERT INTO `player` VALUES (47,1,47,752687236,'佳佳！',0,1505034077);
INSERT INTO `player` VALUES (48,1,48,1102740911,'Thousand aster',0,1505034077);
INSERT INTO `player` VALUES (49,1,49,1386834399,'王志文',0,1505034077);
INSERT INTO `player` VALUES (50,1,50,922368156,'大良风之子',0,1505034077);
INSERT INTO `player` VALUES (51,1,51,1478927499,'小魔鬼KK',0,1505034077);
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
  `is_forbidden` tinyint(4) NOT NULL COMMENT '是否禁用:1是0否',
  `create_time` int(11) NOT NULL COMMENT '注册时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,0,'','admin','admin','','','123456','',1,20,2,0,1499654426);
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

-- Dump completed on 2017-09-12 16:03:38
