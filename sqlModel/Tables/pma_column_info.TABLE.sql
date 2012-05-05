CREATE TABLE `pma_column_info` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `db_name` varchar(64) DEFAULT NULL,
  `table_name` varchar(64) DEFAULT NULL,
  `column_name` varchar(64) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `mimetype` varchar(255) DEFAULT NULL,
  `transformation` varchar(255) DEFAULT NULL,
  `transformation_options` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Column Information for phpMyAdmin'