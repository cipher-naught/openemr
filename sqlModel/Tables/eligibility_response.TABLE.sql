CREATE TABLE `eligibility_response` (
  `response_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `response_description` varchar(255) DEFAULT NULL,
  `response_status` enum('A','D') NOT NULL DEFAULT 'A',
  `response_vendor_id` bigint(20) DEFAULT NULL,
  `response_create_date` date DEFAULT NULL,
  `response_modify_date` date DEFAULT NULL,
  PRIMARY KEY (`response_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1