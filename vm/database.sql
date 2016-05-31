--
-- Persistent SQL Database for XIVSync
--

CREATE TABLE `pending_characters` (
  `lodestone_id` int(32) NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`lodestone_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1

CREATE TABLE `pending_freecompanies` (
  `fc_id` varchar(64) NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1

CREATE TABLE `pending_linkshells` (
  `ls_id` varchar(64) NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ls_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1
