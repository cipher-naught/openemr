CREATE TABLE `lang_custom` (
  `lang_description` varchar(100) NOT NULL DEFAULT '',
  `lang_code` char(2) NOT NULL DEFAULT '',
  `constant_name` varchar(255) NOT NULL DEFAULT '',
  `definition` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1