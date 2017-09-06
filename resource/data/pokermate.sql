/*
Navicat MySQL Data Transfer

Source Server         : 192.168.1.177
Source Server Version : 50540
Source Host           : 192.168.1.177:3306
Source Database       : pokermate

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2017-09-06 17:41:17
*/

SET FOREIGN_KEY_CHECKS=0;

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of club
-- ----------------------------
INSERT INTO `club` VALUES ('1', '1', '888分舵', '888520', 'jay', '123456', '0', '1504667409');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of money_type
-- ----------------------------
INSERT INTO `money_type` VALUES ('1', '1', '支付宝', '0', '0', '1504680294');
INSERT INTO `money_type` VALUES ('2', '1', '微信', '0', '0', '1504682724');
INSERT INTO `money_type` VALUES ('3', '1', '银行卡', '0', '0', '1504688074');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', '0', '', 'admin', 'admin', '', '', '123456', '', '1', '20', '1', '0', '1499654426');
