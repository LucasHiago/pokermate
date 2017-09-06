/*
Navicat MySQL Data Transfer

Source Server         : phpstudyLocalhost
Source Server Version : 50547
Source Host           : localhost:3306
Source Database       : pokermate

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2017-09-07 04:26:45
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `agent`
-- ----------------------------
DROP TABLE IF EXISTS `agent`;
CREATE TABLE `agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `agent_name` varchar(50) NOT NULL COMMENT '代理名字',
  `is_delete` tinyint(4) NOT NULL COMMENT '是否删除:1是0否',
  `create_time` int(11) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of agent
-- ----------------------------

-- ----------------------------
-- Table structure for `club`
-- ----------------------------
DROP TABLE IF EXISTS `club`;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of club
-- ----------------------------
INSERT INTO `club` VALUES ('1', '1', '888分舵', '888520', 'jay', '123456', '0', '1504667409');

-- ----------------------------
-- Table structure for `fencheng_setting`
-- ----------------------------
DROP TABLE IF EXISTS `fencheng_setting`;
CREATE TABLE `fencheng_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `zhuozi_jibie` varchar(50) NOT NULL COMMENT '桌子级别',
  `yingfan` decimal(10,2) NOT NULL COMMENT '赢返',
  `shufan` decimal(10,2) NOT NULL COMMENT '输返',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of fencheng_setting
-- ----------------------------

-- ----------------------------
-- Table structure for `import_data`
-- ----------------------------
DROP TABLE IF EXISTS `import_data`;
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
  `paiju_id` int(11) NOT NULL COMMENT '牌局ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of import_data
-- ----------------------------
INSERT INTO `import_data` VALUES ('1', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '735304085', '妖股', '21955639', '新西兰27poker', '800', '657', '0', '0', '0', '0', '295', '-143', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('2', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '481124234', 'DAB621', '21340895', '渔船', '400', '1337', '0', '0', '0', '0', '295', '937', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('3', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '441053099', 'PersonZ', '21340895', '渔船', '400', '383', '0', '0', '0', '0', '295', '-17', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('4', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '796020048', '神相李布衣', '21065196', '海洋馆', '400', '400', '0', '0', '0', '8', '295', '0', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('5', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '1272637032', '阿玮（TW）', '21065196', '海洋馆', '400', '79', '8', '0', '-8', '8', '295', '-321', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('6', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '1292548547', '厚先生', '2767536', '888国际', '1000', '1447', '0', '0', '0', '0', '295', '447', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('7', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '94816688', '冰泉水', '2767536', '888国际', '400', '580', '0', '0', '0', '0', '295', '180', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('8', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '1490946566', '买牌买到底', '2767536', '888国际', '600', '507', '0', '0', '0', '0', '295', '-93', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('9', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '1232996650', '高尔基', '1798364', '?村长家?', '400', '400', '0', '0', '0', '14', '295', '0', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('10', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '1355487424', '哈气', '1798364', '?村长家?', '400', '0', '14', '0', '-14', '14', '295', '-400', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('11', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '1239328724', 'releeyk0', '568568', '小肥羊俱乐部', '800', '1272', '273', '0', '-273', '273', '295', '472', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('12', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '1191529058', '價值下注', '568568', '小肥羊俱乐部', '600', '984', '0', '0', '0', '273', '295', '384', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('13', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '1130923485', 'AAFish', '568568', '小肥羊俱乐部', '800', '991', '0', '0', '0', '273', '295', '191', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('14', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '1355192654', 'MU Jheng', '568568', '小肥羊俱乐部', '1200', '1270', '0', '0', '0', '273', '295', '70', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('15', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '1608005878', '踩过介', '568568', '小肥羊俱乐部', '600', '600', '0', '0', '0', '273', '295', '0', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('16', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '1626414532', '人數字', '568568', '小肥羊俱乐部', '400', '0', '0', '0', '0', '273', '295', '-400', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('17', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '1413166582', '稀粥炒饭', '555558', '打死为止小班?', '1000', '913', '0', '0', '0', '0', '295', '-87', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('18', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '54381787', '深海小醜?', '555558', '打死为止小班?', '400', '0', '0', '0', '0', '0', '295', '-400', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('19', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '1015655431', '美少女月野兔', '555558', '打死为止小班?', '800', '217', '0', '0', '0', '0', '295', '-583', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('20', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '1032824372', '狼狼的刷', '288888', '金字塔俱乐部', '800', '0', '0', '0', '0', '0', '295', '-800', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');
INSERT INTO `import_data` VALUES ('21', '普通保险局', '2/4?1181A', '海洋馆导游', '2/4', '8', '3.0', '242', '1157888537', '老虎一号', '227733', '水上公园轻级别', '400', '666', '0', '0', '0', '0', '295', '266', '2017-08-12 07:30:58', '1502494258', '1504729277', '1');

-- ----------------------------
-- Table structure for `keren_benjin`
-- ----------------------------
DROP TABLE IF EXISTS `keren_benjin`;
CREATE TABLE `keren_benjin` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `keren_bianhao` int(11) NOT NULL COMMENT '客人编号',
  `benjin` int(11) NOT NULL COMMENT '本金',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of keren_benjin
-- ----------------------------
INSERT INTO `keren_benjin` VALUES ('1', '1', '0');
INSERT INTO `keren_benjin` VALUES ('2', '2', '0');
INSERT INTO `keren_benjin` VALUES ('3', '3', '0');
INSERT INTO `keren_benjin` VALUES ('4', '4', '0');
INSERT INTO `keren_benjin` VALUES ('5', '5', '0');
INSERT INTO `keren_benjin` VALUES ('6', '6', '0');
INSERT INTO `keren_benjin` VALUES ('7', '7', '0');
INSERT INTO `keren_benjin` VALUES ('8', '8', '0');
INSERT INTO `keren_benjin` VALUES ('9', '9', '0');
INSERT INTO `keren_benjin` VALUES ('10', '10', '0');
INSERT INTO `keren_benjin` VALUES ('11', '11', '0');
INSERT INTO `keren_benjin` VALUES ('12', '12', '0');
INSERT INTO `keren_benjin` VALUES ('13', '13', '0');
INSERT INTO `keren_benjin` VALUES ('14', '14', '0');
INSERT INTO `keren_benjin` VALUES ('15', '15', '0');
INSERT INTO `keren_benjin` VALUES ('16', '16', '0');
INSERT INTO `keren_benjin` VALUES ('17', '17', '0');
INSERT INTO `keren_benjin` VALUES ('18', '18', '0');
INSERT INTO `keren_benjin` VALUES ('19', '19', '0');
INSERT INTO `keren_benjin` VALUES ('20', '20', '0');
INSERT INTO `keren_benjin` VALUES ('21', '21', '0');

-- ----------------------------
-- Table structure for `lianmeng`
-- ----------------------------
DROP TABLE IF EXISTS `lianmeng`;
CREATE TABLE `lianmeng` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `name` varchar(50) NOT NULL COMMENT '联盟名称',
  `qianzhang` int(11) NOT NULL COMMENT '联盟欠账',
  `duizhangfangfa` int(11) NOT NULL COMMENT '对账方法（1：0.975 2：无水账单）',
  `paiju_fee` int(11) NOT NULL COMMENT '上缴桌费',
  `baoxian_choucheng` int(11) NOT NULL COMMENT '保险抽成',
  `is_delete` tinyint(4) NOT NULL COMMENT '是否删除:1是0否',
  `create_time` int(11) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of lianmeng
-- ----------------------------

-- ----------------------------
-- Table structure for `money_out_put_type`
-- ----------------------------
DROP TABLE IF EXISTS `money_out_put_type`;
CREATE TABLE `money_out_put_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `out_put_type` varchar(50) NOT NULL COMMENT '支出方式',
  `money` int(11) NOT NULL COMMENT '金额',
  `is_delete` tinyint(4) NOT NULL COMMENT '是否删除:1是0否',
  `create_time` int(11) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of money_out_put_type
-- ----------------------------
INSERT INTO `money_out_put_type` VALUES ('1', '1', '伙食', '0', '0', '1504689955');
INSERT INTO `money_out_put_type` VALUES ('2', '1', '电费', '0', '0', '1504689973');

-- ----------------------------
-- Table structure for `money_type`
-- ----------------------------
DROP TABLE IF EXISTS `money_type`;
CREATE TABLE `money_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `pay_type` varchar(50) NOT NULL COMMENT '支付方式',
  `money` int(11) NOT NULL COMMENT '金额',
  `is_delete` tinyint(4) NOT NULL COMMENT '是否删除:1是0否',
  `create_time` int(11) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of money_type
-- ----------------------------
INSERT INTO `money_type` VALUES ('1', '1', '支付宝', '0', '0', '1504680294');
INSERT INTO `money_type` VALUES ('2', '1', '微信', '0', '0', '1504682724');
INSERT INTO `money_type` VALUES ('3', '1', '银行卡', '0', '0', '1504688074');

-- ----------------------------
-- Table structure for `paiju`
-- ----------------------------
DROP TABLE IF EXISTS `paiju`;
CREATE TABLE `paiju` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `paiju_name` varchar(50) NOT NULL COMMENT '牌局名',
  `end_time` int(11) NOT NULL COMMENT '结束时间戳',
  `status` tinyint(4) NOT NULL COMMENT '牌局状态:0未结算1已结算2已交班',
  `create_time` int(11) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of paiju
-- ----------------------------
INSERT INTO `paiju` VALUES ('1', '2/4?1181A', '1502494258', '0', '1504716647');

-- ----------------------------
-- Table structure for `player`
-- ----------------------------
DROP TABLE IF EXISTS `player`;
CREATE TABLE `player` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID自增',
  `keren_bianhao` int(11) NOT NULL COMMENT '客人编号',
  `player_id` int(11) NOT NULL COMMENT '玩家游戏ID',
  `player_name` varchar(50) NOT NULL COMMENT '玩家游戏名字',
  `ying_chou` decimal(10,2) NOT NULL COMMENT '赢抽点数',
  `shu_fan` decimal(10,2) NOT NULL COMMENT '输返点数',
  `agent_id` int(10) NOT NULL COMMENT '代理人ID',
  `remark` varchar(500) NOT NULL COMMENT '备注',
  `is_delete` tinyint(4) NOT NULL COMMENT '是否删除:1是0否',
  `create_time` int(11) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of player
-- ----------------------------
INSERT INTO `player` VALUES ('1', '1', '735304085', '妖股', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('2', '2', '481124234', 'DAB621', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('3', '3', '441053099', 'PersonZ', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('4', '4', '796020048', '神相李布衣', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('5', '5', '1272637032', '阿玮（TW）', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('6', '6', '1292548547', '厚先生', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('7', '7', '94816688', '冰泉水', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('8', '8', '1490946566', '买牌买到底', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('9', '9', '1232996650', '高尔基', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('10', '10', '1355487424', '哈气', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('11', '11', '1239328724', 'releeyk0', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('12', '12', '1191529058', '價值下注', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('13', '13', '1130923485', 'AAFish', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('14', '14', '1355192654', 'MU Jheng', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('15', '15', '1608005878', '踩过介', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('16', '16', '1626414532', '人數字', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('17', '17', '1413166582', '稀粥炒饭', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('18', '18', '54381787', '深海小醜?', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('19', '19', '1015655431', '美少女月野兔', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('20', '20', '1032824372', '狼狼的刷', '0.00', '0.00', '0', '', '0', '1504729277');
INSERT INTO `player` VALUES ('21', '21', '1157888537', '老虎一号', '0.00', '0.00', '0', '', '0', '1504729277');

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
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
  `choushui_shuanfa` tinyint(4) NOT NULL COMMENT '抽水算法',
  `is_forbidden` tinyint(4) NOT NULL COMMENT '是否禁用:1是0否',
  `create_time` int(11) NOT NULL COMMENT '注册时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', '0', '', 'admin', 'admin', '', '', '123456', '', '1', '20', '1', '0', '1499654426');
