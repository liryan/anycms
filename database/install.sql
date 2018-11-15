/*
SQLyog  v12.2.6 (64 bit)
MySQL - 5.7.20-log : Database - admlite
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*Table structure for table `t_acl` */

DROP TABLE IF EXISTS `t_acl`;

CREATE TABLE `t_acl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roleid` int(11) DEFAULT NULL,
  `priitem` int(11) NOT NULL COMMENT '0超级管理员, >0 权限项目：菜单id / 表ID',
  `value` varchar(16) DEFAULT NULL COMMENT '具体设置参数',
  `updated_at` timestamp NOT NULL,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

/*Data for the table `t_acl` */

/*Table structure for table `t_admin` */

DROP TABLE IF EXISTS `t_admin`;

CREATE TABLE `t_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(64) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `name` varchar(32) DEFAULT NULL,
  `role` varchar(256) DEFAULT NULL,
  `avatar` varchar(256) DEFAULT NULL,
  `setting` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Table structure for table `t_setting` */

DROP TABLE IF EXISTS `t_setting`;

CREATE TABLE `t_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentid` int(11) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `order` int(11) DEFAULT '0',
  `type` tinyint(4) DEFAULT '0',
  `setting` text,
  `updated_at` timestamp NOT NULL,
  `created_at` timestamp NOT NULL,
  `status` tinyint(4) DEFAULT '1',
  `note` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=utf8;

/*Data for the table `t_setting` */

insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values 

(1,0,'栏目',0,1,'','2017-02-06 06:41:34','2017-02-06 06:41:34',1,'栏目'),

(2,0,'菜单',0,2,'','2018-06-27 09:47:04','2017-02-06 06:42:15',1,'菜单'),

(3,0,'常量',0,3,'','2017-02-06 07:13:14','2017-02-06 07:13:14',1,'常量'),

(4,0,'模型',0,4,'','2017-02-06 07:13:47','2017-02-06 07:13:47',1,'模型'),

(5,0,'角色',0,5,'','2017-02-06 07:13:47','2017-02-06 07:13:47',1,'角色'),

(6,0,'系统设置',0,6,'','2018-07-19 15:36:16','2017-02-06 18:13:13',1,'设置'),

(7,0,'个人设置',0,7,NULL,'2018-07-19 15:36:16','2018-06-27 09:54:32',1,'personal'),

(8,0,'统计设置',0,8,'fa fa-line-chart','2018-07-20 14:18:33','2018-07-19 15:22:52',1,'stat'),

(10,2,'自定义菜单',0,0,NULL,'2018-06-27 13:46:20','2018-06-27 13:46:20',1,'/'),

(11,2,'系统菜单',0,0,NULL,'2018-06-27 13:46:00','2018-06-27 13:45:03',1,'/'),

(12,11,'模型管理',0,0,'fa fa-table','2018-06-27 14:20:37','2018-06-27 13:40:21',1,'/admin/model'),

(13,11,'常量管理',0,0,'fa fa-chain','2018-07-20 14:23:34','2018-06-27 13:40:40',1,'/admin/const'),

(14,11,'频道管理',0,0,'fa fa-sitemap','2018-06-27 14:21:02','2018-06-27 13:40:42',1,'/admin/category'),

(15,11,'菜单管理',0,0,'fa fa-list','2018-07-20 14:23:24','2018-06-27 13:47:03',1,'/admin/menu'),

(16,11,'统计配置',0,0,'fa fa-line-chart','2018-07-20 14:18:46','2018-07-19 17:49:01',1,'/admin/stat'),

(17,11,'角色管理',0,0,'fa fa-graduation-cap','2018-07-19 17:48:29','2018-06-27 13:41:50',1,'/admin/privilege'),

(18,11,'账号管理',0,0,'fa fa-user-plus','2018-07-19 17:48:25','2018-06-27 13:42:40',1,'/admin/account'),

(20,5,'超级管理员',0,0,'','2018-06-27 09:55:55','2017-02-06 18:07:28',1,'admin')

