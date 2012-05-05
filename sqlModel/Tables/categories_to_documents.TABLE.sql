CREATE TABLE `categories_to_documents` (
  `category_id` int(11) NOT NULL DEFAULT '0',
  `document_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`,`document_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1