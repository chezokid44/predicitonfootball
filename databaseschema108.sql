-- Prediction League 1.08 schema, updated for MySQL 8.
SET NAMES utf8mb4;

--
-- Table structure for table 'plconfigdata'
--

CREATE TABLE `plconfigdata` (
  `lid` INT NOT NULL DEFAULT 0,
  `grp` INT NOT NULL DEFAULT 0,
  `param` VARCHAR(32) NOT NULL DEFAULT '',
  `value` VARCHAR(90) NOT NULL DEFAULT '',
  `descr` TEXT NOT NULL,
  `ro` ENUM('Y','N') DEFAULT 'N',
  PRIMARY KEY (`lid`,`param`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table 'plmatchdata'
--

CREATE TABLE `plmatchdata` (
  `lid` INT NOT NULL DEFAULT 0,
  `matchid` INT NOT NULL AUTO_INCREMENT,
  `matchdate` DATETIME NOT NULL,
  `hometeam` VARCHAR(30) NOT NULL DEFAULT '',
  `awayteam` VARCHAR(30) NOT NULL DEFAULT '',
  `homescore` SMALLINT UNSIGNED DEFAULT NULL,
  `awayscore` SMALLINT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`lid`,`matchid`),
  KEY `idx_matchid_auto` (`matchid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table 'plmsgdata'
--

CREATE TABLE `plmsgdata` (
  `lid` INT NOT NULL DEFAULT 0,
  `msgid` INT NOT NULL AUTO_INCREMENT,
  `threadid` INT DEFAULT NULL,
  `prevmsg` INT DEFAULT NULL,
  `sender` INT NOT NULL DEFAULT 0,
  `receiver` INT NOT NULL DEFAULT 0,
  `subject` VARCHAR(255) DEFAULT NULL,
  `body` TEXT,
  `status` ENUM('new','read','deleted') DEFAULT NULL,
  `msgtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`lid`,`msgid`),
  KEY `idx_msgid_auto` (`msgid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table 'plpredictiondata'
--

CREATE TABLE `plpredictiondata` (
  `lid` INT NOT NULL DEFAULT 0,
  `userid` INT NOT NULL DEFAULT 0,
  `matchid` INT NOT NULL DEFAULT 0,
  `predtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `homescore` SMALLINT UNSIGNED DEFAULT NULL,
  `awayscore` SMALLINT UNSIGNED DEFAULT NULL,
  `isauto` ENUM('Y','N') DEFAULT 'N',
  PRIMARY KEY (`lid`,`userid`,`matchid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table 'plstandings'
--

CREATE TABLE `plstandings` (
  `lid` INT NOT NULL DEFAULT 0,
  `userid` INT NOT NULL DEFAULT 0,
  `position` SMALLINT NOT NULL DEFAULT 0,
  `pld` INT UNSIGNED DEFAULT NULL,
  `won` INT UNSIGNED DEFAULT NULL,
  `drawn` INT UNSIGNED DEFAULT NULL,
  `lost` INT UNSIGNED DEFAULT NULL,
  `gfor` SMALLINT UNSIGNED DEFAULT NULL,
  `gagainst` SMALLINT UNSIGNED DEFAULT NULL,
  `diff` SMALLINT DEFAULT NULL,
  `points` INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`lid`,`userid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table 'pluserdata'
--

CREATE TABLE `pluserdata` (
  `lid` INT NOT NULL DEFAULT 0,
  `userid` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(32) NOT NULL DEFAULT '',
  `password` VARCHAR(40) DEFAULT NULL,
  `email` VARCHAR(60) DEFAULT NULL,
  `icon` VARCHAR(128) DEFAULT NULL,
  `lang` VARCHAR(32) DEFAULT NULL,
  `usertype` SMALLINT DEFAULT NULL,
  `dflths` SMALLINT DEFAULT 0,
  `dfltas` SMALLINT DEFAULT 0,
  `since` DATE DEFAULT NULL,
  `auto` ENUM('Y','N') DEFAULT 'N',
  PRIMARY KEY (`lid`,`userid`),
  KEY `idx_userid_auto` (`userid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

