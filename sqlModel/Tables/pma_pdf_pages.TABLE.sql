CREATE TABLE `pma_pdf_pages` (
  `db_name` varchar(64) DEFAULT NULL,
  `page_nr` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_descr` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`page_nr`),
  KEY `db_name` (`db_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='PDF Relationpages for PMA'