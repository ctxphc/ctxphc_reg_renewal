CREATE TABLE `ctxphc_registration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(55) NOT NULL,
  `last_name` varchar(55) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `birthday` varchar(2) NOT NULL,
  `membership_id` varchar(2) NOT NULL,
  `reg_date` datetime NOT NULL,
  `addr1` varchar(200) NOT NULL,
  `addr2` varchar(55) NOT NULL,
  `city` varchar(75) NOT NULL,
  `state` varchar(2) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `occupation` varchar(75) NOT NULL,
  `sp_first_name` varchar(55) NOT NULL,
  `sp_last_name` varchar(55) NOT NULL,
  `sp_email` varchar(255) NOT NULL,
  `sp_phone` varchar(12) NOT NULL,
  `sp_birthday` varchar(2) NOT NULL,
  `sp_relationship_id` varchar(2) NOT NULL,
  `c1_first_name` varchar(55) NOT NULL,
  `c1_last_name` varchar(55) NOT NULL,
  `c1_email` varchar(255) NOT NULL,
  `c1_phone` varchar(12) NOT NULL,
  `c1_birthday` varchar(2) NOT NULL,
  `c1_relationship_id` varchar(2) NOT NULL,
  `c2_first_name` varchar(55) NOT NULL,
  `c2_last_name` varchar(55) NOT NULL,
  `c2_email` varchar(255) NOT NULL,
  `c2_phone` varchar(12) NOT NULL,
  `c2_birthday` varchar(2) NOT NULL,
  `c2_relationship_id` varchar(2) NOT NULL,
  `c3_first_name` varchar(55) NOT NULL,
  `c3_last_name` varchar(55) NOT NULL,
  `c3_email` varchar(255) NOT NULL,
  `c3_phone` varchar(12) NOT NULL,
  `c3_birthday` varchar(2) NOT NULL,
  `c3_relationship_id` varchar(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `ctxphc_members` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(25) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `email` varchar(254) DEFAULT NULL,
  `phone` varchar(13) DEFAULT NULL,
  `occupation` varchar(75) DEFAULT NULL,
  `birthday` timestamp DEFAULT NULL,
  `hatch_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tag_date` datetime DEFAULT NULL,
  `initiated_date` datetime DEFAULT NULL,
  `renewal_date` datetime DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `membership_id` int(2) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `relationship_id` int(11) NOT NULL,
  `memb_id` int(11) DEFAULT NULL,
  `wp_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ctxphc_memb_cost` (
  `id` int(11) NOT NULL,
  `id_cost` int(11) NOT NULL,
  `ic_cost` int(11) NOT NULL,
  `co_cost` int(11) NOT NULL,
  `hh_cost` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ctxphc_member_addresses` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `addr1` varchar(100) NOT NULL,
  `addr2` varchar(15) DEFAULT NULL,
  `city` varchar(45) NOT NULL,
  `state` varchar(2) NOT NULL,
  `zip` varchar(10) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=287 DEFAULT CHARSET=utf8;

CREATE TABLE `ctxphc_member_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `memb_id` int(11) NOT NULL,
  `payer_email` varchar(125) NOT NULL,
  `payment_gross` float(9,2) NOT NULL,
  `payment_date` date NOT NULL,
  `pmnt_complete` enum('Y','N') NOT NULL DEFAULT 'N',
  `txn_id` int(11) NOT NULL,
  `ipn_track_id` varchar(25) NOT NULL,
  `ipn_data` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ctxphc_member_relationships` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `relationship_type` varchar(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

CREATE TABLE `ctxphc_member_status` (
  `ID` int(11) NOT NULL,
  `memb_status` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ctxphc_membership_pricing` (
  `ID` int(11) NOT NULL,
  `id_cost` int(11) NOT NULL,
  `ic_cost` int(11) NOT NULL,
  `co_cost` int(11) NOT NULL,
  `hh_cost` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `unique_id` (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ctxphc_membership_types` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `memb_type` char(2) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `memb_type_UNIQUE` (`memb_type`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


CREATE TABLE `ctxphc_members_backup` (
  `ID` int(11) NOT NULL,
  `first_name` varchar(25) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `email` varchar(254) DEFAULT NULL,
  `phone` varchar(13) DEFAULT NULL,
  `occupation` varchar(75) DEFAULT NULL,
  `birthday` varchar(5) DEFAULT NULL,
  `hatch_date` timestamp NOT NULL,
  `tag_date` datetime DEFAULT NULL,
  `initiated_date` datetime DEFAULT NULL,
  `renewal_date` datetime DEFAULT NULL,
  `status_id` int(9) DEFAULT NULL,
  `membership_id` int(2) DEFAULT NULL,
  `address_id` int(9) DEFAULT NULL,
  `relationship_id` int(2) NOT NULL,
  `memb_id` int(11) DEFAULT NULL,
  `wp_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;