CREATE TABLE `lang_definitions` (
  `def_id` int(11) NOT NULL AUTO_INCREMENT,
  `cons_id` int(11) NOT NULL DEFAULT '0',
  `lang_id` int(11) NOT NULL DEFAULT '0',
  `definition` mediumtext,
  UNIQUE KEY `def_id` (`def_id`),
  KEY `cons_id` (`cons_id`)
) ENGINE=MyISAM AUTO_INCREMENT=29513 DEFAULT CHARSET=latin1