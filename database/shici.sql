/*
SQLyog Community v11.51 (32 bit)
MySQL - 5.7.17-log : Database - shici
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`shici` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `shici`;

/*Table structure for table `t_authorinfo` */

DROP TABLE IF EXISTS `t_authorinfo`;

CREATE TABLE `t_authorinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `text` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `age` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `author` (`name`),
  KEY `age` (`age`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `t_authorinfo` */

insert  into `t_authorinfo`(`id`,`name`,`text`,`updated_at`,`created_at`,`age`) values (1,'贺知章','　　贺知章(659—744)，字季真，号四明狂客，汉族，唐越州（今绍兴）永兴(今浙江萧山)人，贺知章诗文以绝句见长，除祭神乐章、应制诗外，其写景、抒怀之作风格独特，清新潇洒，著名的《咏柳》、《回乡偶书》两首脍炙人口，千古传诵，今尚存录入《全唐诗》共19首。','2017-03-03 11:49:42','2017-03-03 11:49:42','唐代');

/*Table structure for table `t_comment` */

DROP TABLE IF EXISTS `t_comment`;

CREATE TABLE `t_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contentid` int(11) DEFAULT NULL,
  `text` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`contentid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `t_comment` */


/*Table structure for table `t_content` */

DROP TABLE IF EXISTS `t_content`;

CREATE TABLE `t_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text,
  `author` varchar(32) DEFAULT NULL,
  `title` varchar(64) DEFAULT NULL,
  `age` varchar(32) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `tag` varchar(32) DEFAULT NULL,
  `keyword` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `authorid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `authorid` (`authorid`),
  FULLTEXT KEY `keyword` (`keyword`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `t_content` */


/*Table structure for table `t_note` */

DROP TABLE IF EXISTS `t_note`;

CREATE TABLE `t_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contentid` int(11) DEFAULT NULL,
  `text` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`contentid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `t_note` */


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
