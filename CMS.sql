CREATE DATABASE IF NOT EXISTS `CMS`;
USE `CMS`;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
	`id` int(10) unsigned NOT NULL,
	username varchar(64) NOT NULL,
	`password` varchar(255) NOT NULL,
	perm int(11) NOT NULL DEFAULT '0',
	email varchar(64) NOT NULL,
	date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE KEY username_UNIQUE (username),
	UNIQUE KEY email_UNIQUE (email)
);