CREATE TABLE `integration_mapping` (
  `id` int(11) NOT NULL DEFAULT '0',
  `foreign_id` int(11) NOT NULL DEFAULT '0',
  `foreign_table` varchar(125) DEFAULT NULL,
  `local_id` int(11) NOT NULL DEFAULT '0',
  `local_table` varchar(125) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `foreign_id` (`foreign_id`,`foreign_table`,`local_id`,`local_table`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1