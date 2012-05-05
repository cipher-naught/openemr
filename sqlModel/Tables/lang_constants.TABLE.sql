CREATE TABLE `lang_constants` (
  `cons_id` int(11) NOT NULL AUTO_INCREMENT,
  `constant_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  UNIQUE KEY `cons_id` (`cons_id`),
  KEY `constant_name` (`constant_name`)
) ENGINE=MyISAM AUTO_INCREMENT=4725 DEFAULT CHARSET=latin1