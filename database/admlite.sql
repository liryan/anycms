-- MySQL dump 10.13  Distrib 5.7.21, for Linux (x86_64)
--
-- Host: localhost    Database: admlite
-- ------------------------------------------------------
-- Server version	5.7.21

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `t_acl`
--

DROP TABLE IF EXISTS `t_acl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_acl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roleid` int(11) DEFAULT NULL,
  `priitem` int(11) NOT NULL COMMENT '0超级管理员, >0 权限项目：菜单id / 表ID',
  `value` varchar(16) DEFAULT NULL COMMENT '具体设置参数',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_acl`
--

LOCK TABLES `t_acl` WRITE;
/*!40000 ALTER TABLE `t_acl` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_acl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_admin`
--

DROP TABLE IF EXISTS `t_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_admin`
--

LOCK TABLES `t_admin` WRITE;
/*!40000 ALTER TABLE `t_admin` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_setting`
--

DROP TABLE IF EXISTS `t_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentid` int(11) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `order` int(11) DEFAULT '0',
  `type` tinyint(4) DEFAULT '0',
  `setting` text,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT '1',
  `note` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_setting`
--

LOCK TABLES `t_setting` WRITE;
/*!40000 ALTER TABLE `t_setting` DISABLE KEYS */;
INSERT INTO `t_setting` VALUES (1,0,'栏目',0,1,'','2017-02-05 22:41:34','2017-02-05 22:41:34',1,'栏目'),(2,0,'菜单',0,2,'','2018-06-27 01:47:04','2017-02-05 22:42:15',1,'菜单'),(3,0,'常量',0,3,'','2017-02-05 23:13:14','2017-02-05 23:13:14',1,'常量'),(4,0,'模型',0,4,'','2017-02-05 23:13:47','2017-02-05 23:13:47',1,'模型'),(5,0,'角色',0,5,'','2017-02-05 23:13:47','2017-02-05 23:13:47',1,'角色'),(6,0,'系统设置',0,0,'','2018-06-27 01:51:26','2017-02-06 10:13:13',1,'设置'),(7,0,'个人设置',0,0,NULL,'2018-06-27 01:54:55','2018-06-27 01:54:32',1,'personal'),(10,2,'自定义菜单',0,0,NULL,'2018-06-27 05:46:20','2018-06-27 05:46:20',1,'/'),(11,2,'系统菜单',0,0,NULL,'2018-06-27 05:46:00','2018-06-27 05:45:03',1,'/'),(12,11,'模型管理',0,0,'fa fa-table','2018-06-27 06:20:37','2018-06-27 05:40:21',1,'/admin/model'),(13,11,'常量管理',0,0,'fa fa-bars','2018-06-27 06:20:50','2018-06-27 05:40:40',1,'/admin/const'),(14,11,'频道管理',0,0,'fa fa-sitemap','2018-06-27 06:21:02','2018-06-27 05:40:42',1,'/admin/category'),(15,11,'菜单管理',0,0,'fa fa-list','2018-06-27 06:34:28','2018-06-27 05:47:03',1,'/admin/menu'),(16,11,'角色管理',0,0,'fa fa-graduation-cap','2018-06-27 06:32:03','2018-06-27 05:41:50',1,'/admin/privilege'),(17,11,'账号管理',0,0,'fa fa-user-plus','2018-06-27 06:34:17','2018-06-27 05:42:40',1,'/admin/account'),(20,5,'超级管理员',0,0,'','2018-06-27 01:55:55','2017-02-06 10:07:28',1,'admin');
/*!40000 ALTER TABLE `t_setting` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-06-29 15:14:28
