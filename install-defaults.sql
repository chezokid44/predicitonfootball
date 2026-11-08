-- =====================================================================
-- Prediction League 1.13 - schema + default configuration
--
-- Safe to run on an EXISTING database:
--   * CREATE TABLE IF NOT EXISTS  -> only creates tables that are missing
--   * INSERT IGNORE               -> only adds config rows that are missing,
--                                    never overwrites settings you've changed
--
-- Import it in phpMyAdmin (select your database first, then Import), or:
--   mysql -u USER -p DATABASE < install-defaults.sql
--
-- Table names and league ID (0) match the defaults in systemvars.php
-- ($dbaseConfigData = "plconfigdata", $leagueID = "0" etc.). If you
-- changed them there, change them here too.
-- No explicit collation is set, so it works on MySQL 8 and MariaDB.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `plconfigdata` (
  `lid` INT NOT NULL DEFAULT 0,
  `grp` INT NOT NULL DEFAULT 0,
  `param` VARCHAR(32) NOT NULL DEFAULT '',
  `value` VARCHAR(90) NOT NULL DEFAULT '',
  `descr` TEXT NOT NULL,
  `ro` ENUM('Y','N') DEFAULT 'N',
  PRIMARY KEY (`lid`,`param`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `plmatchdata` (
  `lid` INT NOT NULL DEFAULT 0,
  `matchid` INT NOT NULL AUTO_INCREMENT,
  `matchdate` DATETIME NOT NULL,
  `hometeam` VARCHAR(30) NOT NULL DEFAULT '',
  `awayteam` VARCHAR(30) NOT NULL DEFAULT '',
  `homescore` SMALLINT UNSIGNED DEFAULT NULL,
  `awayscore` SMALLINT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`lid`,`matchid`),
  KEY `idx_matchid_auto` (`matchid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `plmsgdata` (
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
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `plpredictiondata` (
  `lid` INT NOT NULL DEFAULT 0,
  `userid` INT NOT NULL DEFAULT 0,
  `matchid` INT NOT NULL DEFAULT 0,
  `predtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `homescore` SMALLINT UNSIGNED DEFAULT NULL,
  `awayscore` SMALLINT UNSIGNED DEFAULT NULL,
  `isauto` ENUM('Y','N') DEFAULT 'N',
  PRIMARY KEY (`lid`,`userid`,`matchid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `plstandings` (
  `lid` INT NOT NULL DEFAULT 0,
  `userid` INT NOT NULL DEFAULT 0,
  `position` SMALLINT NOT NULL DEFAULT 0,
  `pld` INT UNSIGNED DEFAULT 0,
  `won` INT UNSIGNED DEFAULT 0,
  `drawn` INT UNSIGNED DEFAULT 0,
  `lost` INT UNSIGNED DEFAULT 0,
  `gfor` SMALLINT UNSIGNED DEFAULT 0,
  `gagainst` SMALLINT UNSIGNED DEFAULT 0,
  `diff` SMALLINT DEFAULT 0,
  `points` INT UNSIGNED DEFAULT 0,
  PRIMARY KEY (`lid`,`userid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pluserdata` (
  `lid` INT NOT NULL DEFAULT 0,
  `userid` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(32) NOT NULL DEFAULT '',
  `password` VARCHAR(40) DEFAULT NULL,
  `email` VARCHAR(60) DEFAULT NULL,
  `icon` VARCHAR(128) DEFAULT 'default.gif',
  `lang` VARCHAR(32) DEFAULT 'english',
  `usertype` SMALLINT DEFAULT 1,
  `dflths` SMALLINT DEFAULT 0,
  `dfltas` SMALLINT DEFAULT 0,
  `since` DATE DEFAULT NULL,
  `auto` ENUM('Y','N') DEFAULT 'N',
  PRIMARY KEY (`lid`,`userid`),
  KEY `idx_userid_auto` (`userid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

-- ---------------------------------------------------------------------
-- Default configuration for league 0 (generated from PopulateConfigTable()
-- in dbasefunctions.php). Edit values afterwards via the admin config page.
-- ---------------------------------------------------------------------
INSERT IGNORE INTO `plconfigdata` (`lid`, `grp`, `param`, `value`, `descr`, `ro`) VALUES
  (0, 1, 'CORR_HOME_SCORE', '1', 'The number of points awarded for predicting the correct home score', 'N'),
  (0, 1, 'CORR_AWAY_SCORE', '1', 'The number of points awarded for predicting the correct away score', 'N'),
  (0, 1, 'CORR_MARGIN', '1', 'Number of points for predicting the correct margin of points.', 'N'),
  (0, 1, 'CORR_RESULT', '1', 'Points awarded for predicting the correct winner or draw, i,e. correct result.', 'N'),
  (0, 1, 'CORR_SCORE', '3', 'Points for predicting the correct score.', 'N'),
  (0, 2, 'PRED_LEAGUE_TITLE', 'Prediction Football.com', 'The title for the prediction league', 'N'),
  (0, 2, 'DEF_ICON', 'default.gif', 'The default icon for the users.', 'N'),
  (0, 2, 'HOME_PAGE_URL', 'http://www.predictionfootball.com', 'Your home page URL. This will be a link from the menu. If you leave this blank, no menu will show up', 'N'),
  (0, 2, 'HOME_PAGE_TITLE', 'Prediction Football.com', 'The title of your home page. Will show as the link on the menu.', 'N'),
  (0, 2, 'ADMIN_EMAIL', '', 'Administrator contact address used for account and system emails.', 'N'),
  (0, 2, 'ADMIN_SIGNATURE', '\nhttp://www.predictionfootball.com/', 'Signature appended to administrator emails.', 'N'),
  (0, 2, 'CHATROOM_URL', '', 'Optional chat-room URL shown in the navigation menu.', 'N'),
  (0, 2, 'LOGFILE', '', 'Optional server logfile path visible to administrators.', 'N'),
  (0, 2, 'DEFAULT_LANGUAGE', 'lang.english.php', 'Default language file, for example lang.english.php.', 'N'),
  (0, 2, 'MULT_USERS', 'FALSE', 'Set this if you want to allow multiple users to share the same email address. Values can be TRUE or FALSE', 'N'),
  (0, 2, 'TZ_OFFSET', '0', 'If your server is in a different time zone than your games, set this to the required offset', 'N'),
  (0, 2, 'USE24HR', 'TRUE', 'Display the time using the 24 hour clock, TRUE or FALSE', 'N'),
  (0, 2, 'REV_USER_PREDS', 'TRUE', 'Change the order in which the user sees their predictions. Values can be TRUE or FALSE', 'N'),
  (0, 2, 'USERS_PER_PAGE', '80', 'The number of users displayed in the table on one page.', 'N'),
  (0, 2, 'MAX_ADMIN_USERS', '1', 'The maximum number of admin users allowed. Keep this to a minimum for security reasons.', 'N'),
  (0, 3, 'USE_MESSAGING', 'TRUE', 'Enable the use of messaging between users. Values can be TRUE or FALSE', 'N'),
  (0, 3, 'HIDE_0_PREDS', 'TRUE', 'Hide the display of users with 0 predictions. Changing this value will require you to recalculate the standings to see the effect. Values can be TRUE or FALSE', 'N'),
  (0, 3, 'NUMMULTFIXTS', '12', 'The number of multiple fixtures to display when adding multiple fixtures.', 'N'),
  (0, 3, 'VIEWUSERPREDS', 'TRUE', 'Can players see each others predictions, TRUE or FALSE', 'N'),
  (0, 3, 'UPLOADICONS', 'TRUE', 'Can users upload their own icons, TRUE or FALSE', 'N'),
  (0, 3, 'ICONSIZE', '50', 'Maximum file size in KB of the icon', 'N'),
  (0, 3, 'PASSWORDENCRYPT', 'TRUE', 'Enable the encryption of user passwords. If you are enabling then the users must use the forgot password function to get their new password. TRUE or FALSE.', 'N'),
  (0, 3, 'LOCKEDGAME', 'FALSE', 'Lock the game so that only admins can create users', 'N'),
  (0, 3, 'AUTO_PREDICT', 'FALSE', 'Enable automatic predictions where supported.', 'N'),
  (0, 4, 'VERSION', '1.13', 'The currently running version of the Prediction League scripts', 'Y');
