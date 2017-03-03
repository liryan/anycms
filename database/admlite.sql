/*
SQLyog Community v11.51 (32 bit)
MySQL - 5.7.17-log : Database - admlite
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`admlite` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `admlite`;

/*Table structure for table `t_acl` */

DROP TABLE IF EXISTS `t_acl`;

CREATE TABLE `t_acl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roleid` int(11) DEFAULT NULL,
  `type` tinyint(4) NOT NULL COMMENT '1:菜单 2:数据表 3.字段',
  `keyid` int(11) NOT NULL COMMENT '0超级管理员, >0 权限项目：菜单id / 表ID',
  `param` text COMMENT '具体设置参数',
  `status` tinyint(4) DEFAULT '1' COMMENT '0:失效 1：有效',
  `updated_at` timestamp NULL,
  `created_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `t_admin` */

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
) ENGINE=InnoDB AUTO_INCREMENT=234 DEFAULT CHARSET=utf8;

/*Data for the table `t_setting` */

insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (1,0,'栏目',0,1,'','2017-02-06 06:41:34','2017-02-06 06:41:34',1,'栏目');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (2,0,'侧栏菜单',0,2,'','2017-02-06 06:42:15','2017-02-06 06:42:15',1,'菜单');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (3,0,'常量',0,3,'','2017-02-06 07:13:14','2017-02-06 07:13:14',1,'常量');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (4,0,'模型',0,4,'','2017-02-06 07:13:47','2017-02-06 07:13:47',1,'模型');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (5,0,'角色',0,5,'','2017-02-06 07:13:47','2017-02-06 07:13:47',1,'角色');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (6,0,'权限',0,6,'','2017-02-06 18:02:20','2017-02-06 18:02:22',1,'权限');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (7,0,'系统设置',0,0,'','2017-02-06 18:13:12','2017-02-06 18:13:13',1,'设置');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (100,6,'增加',0,0,'','2017-02-06 18:06:14','2017-02-06 18:06:17',1,'');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (101,6,'删除',0,0,'','2017-02-06 18:06:20','2017-02-06 18:06:22',1,'');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (102,6,'编辑',0,0,'','2017-02-06 18:06:24','2017-02-06 18:06:26',1,'');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (103,6,'查看',0,0,'','2017-02-06 18:06:28','2017-02-06 18:06:30',1,'');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (200,5,'超级管理员',0,0,'','2017-02-06 18:07:26','2017-02-06 18:07:28',1,'');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (214,4,'member',0,0,NULL,'2017-02-23 11:32:03','2017-02-09 09:48:00',1,'成员表');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (215,3,'颜色',0,0,NULL,'2017-02-10 14:58:17','2017-02-10 14:58:34',1,NULL);
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (216,215,'红色',0,0,NULL,'2017-02-10 14:58:19','2017-02-10 14:58:32',1,NULL);
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (217,215,'绿色',0,0,NULL,'2017-02-10 14:58:20','2017-02-10 14:58:31',1,NULL);
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (218,3,'性别',0,0,NULL,'2017-02-10 14:58:22','2017-02-10 14:58:29',1,NULL);
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (219,218,'男',0,0,NULL,'2017-02-10 14:58:23','2017-02-10 14:58:28',1,NULL);
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (220,218,'女',0,0,NULL,'2017-02-10 14:58:25','2017-02-10 14:58:27',1,NULL);
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (223,214,'age',0,1,'{\"tablename\":\"22\",\"tablefield\":\"333\",\"listable\":\"1\",\"default\":\"2\",\"const\":\"\",\"size\":\"4\",\"searchable\":\"\"}','2017-02-24 07:17:37','2017-02-14 03:18:48',1,'年龄');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (225,214,'sex',0,4,'{\"tablename\":\"11\",\"tablefield\":\"22\",\"listable\":\"1\",\"default\":\"\",\"const\":\"\",\"size\":\"2\"}','2017-02-14 03:24:18','2017-02-14 03:24:18',1,'性别');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (226,214,'address',0,5,'{\"tablename\":\"\",\"tablefield\":\"\",\"listable\":\"\",\"default\":\"\",\"const\":\"217\",\"size\":\"\"}','2017-02-14 06:04:55','2017-02-14 06:04:55',1,'地区');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (228,1,'dalanmu',0,0,'{\"modelid\":\"214\"}','2017-02-17 11:50:23','2017-02-17 11:50:23',1,'大栏目');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (229,228,'1111',0,0,'{\"modelid\":\"214\"}','2017-02-17 11:54:01','2017-02-17 11:54:01',1,'小栏目');
insert  into `t_setting`(`id`,`parentid`,`name`,`order`,`type`,`setting`,`updated_at`,`created_at`,`status`,`note`) values (233,214,'name',0,2,'{\"tablename\":\"\",\"tablefield\":\"\",\"editable\":\"\",\"listable\":\"1\",\"default\":\"\",\"const\":\"\",\"size\":\"16\",\"searchable\":\"\",\"comment\":\"\",\"format\":\"\"}','2017-02-24 11:41:47','2017-02-24 11:41:47',1,'姓名');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
