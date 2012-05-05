CREATE TABLE `openemr_extendedproperties` (
  `extendedpropertiesID` int(11) NOT NULL AUTO_INCREMENT,
  `ObjectType` varchar(255) NOT NULL,
  `ObjectName` varchar(255) NOT NULL,
  `ValueName` varchar(255) NOT NULL,
  `Value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`extendedpropertiesID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1