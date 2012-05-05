CREATE TABLE `pma_relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) DEFAULT NULL,
  `foreign_table` varchar(64) DEFAULT NULL,
  `foreign_field` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  KEY `foreign_field` (`foreign_db`,`foreign_table`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Relation table'