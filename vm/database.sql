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

CREATE TABLE `characters` (
  `lodestone_id` int(32) NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` timestamp NULL DEFAULT NULL,
  `last_active` timestamp NULL DEFAULT NULL,
  `queue` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(64) NOT NULL,
  `server` varchar(32) NOT NULL,
  `avatar` varchar(256) NOT NULL,
  `portrait` varchar(256) NOT NULL,
  `data` text COMMENT 'JSON Data',
  PRIMARY KEY (`lodestone_id`),
  KEY `added` (`added`),
  KEY `last_updated` (`last_updated`),
  KEY `queue` (`queue`),
  KEY `name` (`name`),
  KEY `server` (`server`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1
