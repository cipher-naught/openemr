CREATE TABLE `list_options` (
  `list_id` varchar(31) NOT NULL DEFAULT '',
  `option_id` varchar(31) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `seq` int(11) NOT NULL DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `option_value` float NOT NULL DEFAULT '0',
  `mapping` varchar(31) NOT NULL DEFAULT '',
  `notes` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`list_id`,`option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1