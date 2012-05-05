CREATE TABLE `openemr_postcalendar_limits` (
  `pc_limitid` int(11) NOT NULL AUTO_INCREMENT,
  `pc_catid` int(11) NOT NULL DEFAULT '0',
  `pc_starttime` time NOT NULL DEFAULT '00:00:00',
  `pc_endtime` time NOT NULL DEFAULT '00:00:00',
  `pc_limit` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`pc_limitid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1