-- phpMiniAdmin dump 1.9.140405
-- Datetime: 2014-12-29 08:47:42
-- Host: 
-- Database: u17075

/*!40030 SET NAMES utf8 */;
/*!40030 SET GLOBAL max_allowed_packet=16777216 */;

DROP TABLE IF EXISTS `amocrm_contacts`;
CREATE TABLE `amocrm_contacts` (
  `smcontact_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `atimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_modified` datetime DEFAULT NULL,
  `date_create` datetime DEFAULT NULL,
  `responsible_user_id` int(11) DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`smcontact_id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=105 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `amocrm_leads`;
CREATE TABLE `amocrm_leads` (
  `smlead_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `date_create` datetime DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  `status_id` bigint(20) DEFAULT NULL,
  `price` bigint(20) DEFAULT NULL,
  `responsible_user_id` int(11) DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `atimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`smlead_id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=149 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `amocrm_notes`;
CREATE TABLE `amocrm_notes` (
  `smnote_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL DEFAULT '0',
  `element_id` int(11) NOT NULL DEFAULT '0',
  `element_type` int(11) NOT NULL DEFAULT '0',
  `note_type` int(11) NOT NULL DEFAULT '0',
  `date_create` datetime DEFAULT NULL,
  `created_user_id` int(11) NOT NULL DEFAULT '0',
  `last_modified` datetime DEFAULT NULL,
  `text` text,
  `responsible_user_id` int(11) NOT NULL DEFAULT '0',
  `editable` varchar(1) DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  `atimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`smnote_id`),
  UNIQUE KEY `id` (`id`,`text`(300))
) ENGINE=MyISAM AUTO_INCREMENT=1177 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `amocrm_settings`;
CREATE TABLE `amocrm_settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_name` varchar(255) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  `setting_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `amocrm_tasks`;
CREATE TABLE `amocrm_tasks` (
  `smtask_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL,
  `element_id` int(11) DEFAULT NULL,
  `element_type` int(11) DEFAULT NULL,
  `task_type` varchar(32) CHARACTER SET latin1 DEFAULT NULL,
  `date_create` datetime DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  `text` text,
  `responsible_user_id` int(11) DEFAULT NULL,
  `complete_till` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `server_time` datetime DEFAULT NULL,
  `atimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  `is_viewed` tinyint(4) NOT NULL DEFAULT '0',
  `sm_last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`smtask_id`),
  UNIQUE KEY `id` (`id`,`text`(300),`complete_till`,`last_modified`)
) ENGINE=MyISAM AUTO_INCREMENT=554 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `amocrm_users`;
CREATE TABLE `amocrm_users` (
  `id` int(11) NOT NULL,
  `name` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `last_name` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `login` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `atimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  `sm_last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- phpMiniAdmin dump end
