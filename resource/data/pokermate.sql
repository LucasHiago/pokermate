/*
Navicat MySQL Data Transfer

Source Server         : 192.168.1.177
Source Server Version : 50540
Source Host           : 192.168.1.177:3306
Source Database       : pokermate

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2017-09-04 11:06:20
*/

SET FOREIGN_KEY_CHECKS=0;

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
  `is_forbidden` tinyint(4) NOT NULL COMMENT '是否禁用:1是0否',
  `create_time` int(11) NOT NULL COMMENT '注册时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', '0', '', 'admin', 'admin', '', '', 'e10adc3949ba59abbe56e057f20f883e', '', '1', '0', '1499654426');
