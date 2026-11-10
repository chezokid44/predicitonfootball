-- =====================================================================
-- Prediction League: upgrade for the new HTML/JS front end
--
-- Run once against your existing database (phpMyAdmin > Import, or
--   mysql -u USER -p DATABASE < upgrade.sql).
-- Safe to re-run. Nothing here deletes or overwrites your data.
-- =====================================================================

-- 1. Make sure every table and default setting exists (same as
--    install-defaults.sql: only adds what is missing).
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
  `password` VARCHAR(255) DEFAULT NULL,
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

-- 2. Widen the password column so passwords can be stored with bcrypt.
--    Existing MD5 passwords keep working and are upgraded automatically
--    the next time each player logs in.
ALTER TABLE `pluserdata` MODIFY `password` VARCHAR(255) DEFAULT NULL;

-- 3. Tables used by the new site: activity log and password reset links.
CREATE TABLE IF NOT EXISTS `plactivitylog` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `lid` INT NOT NULL DEFAULT 0,
          `created` DATETIME NOT NULL,
          `userid` INT DEFAULT NULL,
          `username` VARCHAR(32) DEFAULT NULL,
          `ip` VARCHAR(45) DEFAULT NULL,
          `action` VARCHAR(40) NOT NULL,
          `detail` VARCHAR(255) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `idx_lid_created` (`lid`, `created`),
          KEY `idx_action_ip` (`action`, `ip`, `created`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `plresettokens` (
        `token_hash` CHAR(64) NOT NULL,
        `lid` INT NOT NULL DEFAULT 0,
        `userid` INT NOT NULL,
        `created` DATETIME NOT NULL,
        `expires` DATETIME NOT NULL,
        `used` TINYINT NOT NULL DEFAULT 0,
        PRIMARY KEY (`token_hash`),
        KEY `idx_user` (`lid`, `userid`)
      ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pluserprefs` (
          `lid` INT NOT NULL DEFAULT 0,
          `userid` INT NOT NULL,
          `allow_email` TINYINT NOT NULL DEFAULT 0,
          `notify_messages` TINYINT NOT NULL DEFAULT 0,
          PRIMARY KEY (`lid`, `userid`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `plcompetitions` (
          `compid` INT NOT NULL AUTO_INCREMENT,
          `lid` INT NOT NULL DEFAULT 0,
          `name` VARCHAR(40) NOT NULL,
          `short_name` VARCHAR(8) NOT NULL DEFAULT '',
          `position` INT NOT NULL DEFAULT 0,
          PRIMARY KEY (`compid`),
          KEY `idx_lid` (`lid`, `position`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `plmatchcomp` (
          `lid` INT NOT NULL DEFAULT 0,
          `matchid` INT NOT NULL,
          `compid` INT NOT NULL,
          PRIMARY KEY (`lid`, `matchid`),
          KEY `idx_comp` (`lid`, `compid`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

-- Automatic predictions: add to pluserprefs if it was created by an earlier version (skip if the columns exist).
-- ALTER TABLE `pluserprefs` ADD COLUMN `auto_predict` TINYINT NOT NULL DEFAULT 0, ADD COLUMN `auto_since` DATETIME DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `plblocklist` (
          `lid` INT NOT NULL DEFAULT 0,
          `ip` VARCHAR(45) NOT NULL,
          `reason` VARCHAR(100) DEFAULT NULL,
          `created` DATETIME NOT NULL,
          `expires` DATETIME NOT NULL,
          PRIMARY KEY (`lid`, `ip`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

-- Leagues: private leagues, their competitions and members, and the competitions each player plays.
CREATE TABLE IF NOT EXISTS `plleagues` (
          `leagueid` INT NOT NULL AUTO_INCREMENT,
          `lid` INT NOT NULL DEFAULT 0,
          `name` VARCHAR(60) NOT NULL,
          `owner` INT NOT NULL,
          `code` CHAR(8) NOT NULL,
          `all_comps` TINYINT NOT NULL DEFAULT 1,
          `pts_exact` INT NOT NULL DEFAULT 3,
          `pts_result` INT NOT NULL DEFAULT 1,
          `start_at` DATETIME DEFAULT NULL,
          `created` DATETIME NOT NULL,
          PRIMARY KEY (`leagueid`),
          UNIQUE KEY `uniq_code` (`code`),
          KEY `idx_owner` (`lid`, `owner`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `plleaguecomps` (
          `leagueid` INT NOT NULL,
          `compid` INT NOT NULL,
          PRIMARY KEY (`leagueid`, `compid`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `plleaguemembers` (
          `leagueid` INT NOT NULL,
          `userid` INT NOT NULL,
          `role` VARCHAR(10) NOT NULL DEFAULT 'member',
          `joined` DATETIME NOT NULL,
          PRIMARY KEY (`leagueid`, `userid`),
          KEY `idx_user` (`userid`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `plfollows` (
          `lid` INT NOT NULL DEFAULT 0,
          `userid` INT NOT NULL,
          `compid` INT NOT NULL,
          PRIMARY KEY (`lid`, `userid`, `compid`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;


-- Pages written by the admin, and the menu.
CREATE TABLE IF NOT EXISTS `plpages` (
          `pageid` INT NOT NULL AUTO_INCREMENT,
          `lid` INT NOT NULL DEFAULT 0,
          `slug` VARCHAR(60) NOT NULL,
          `title` VARCHAR(100) NOT NULL,
          `body` MEDIUMTEXT NOT NULL,
          `status` VARCHAR(10) NOT NULL DEFAULT 'published',
          `audience` VARCHAR(10) NOT NULL DEFAULT 'all',
          `is_home` TINYINT NOT NULL DEFAULT 0,
          `updated` DATETIME NOT NULL,
          `updated_by` INT DEFAULT NULL,
          PRIMARY KEY (`pageid`),
          UNIQUE KEY `uniq_slug` (`lid`, `slug`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `plmenu` (
          `lid` INT NOT NULL DEFAULT 0,
          `position` INT NOT NULL,
          `item` VARCHAR(40) NOT NULL,
          PRIMARY KEY (`lid`, `position`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;


-- Seasons, teams and which teams are in each competition each season.
CREATE TABLE IF NOT EXISTS `plseasons` (
          `seasonid` INT NOT NULL AUTO_INCREMENT,
          `lid` INT NOT NULL DEFAULT 0,
          `name` VARCHAR(30) NOT NULL,
          `start_date` DATE NOT NULL,
          `end_date` DATE NOT NULL,
          `is_current` TINYINT NOT NULL DEFAULT 0,
          PRIMARY KEY (`seasonid`),
          KEY `idx_dates` (`lid`, `start_date`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `plteams` (
          `teamid` INT NOT NULL AUTO_INCREMENT,
          `lid` INT NOT NULL DEFAULT 0,
          `name` VARCHAR(30) NOT NULL,
          `short_name` VARCHAR(5) NOT NULL DEFAULT '',
          `badge` VARCHAR(80) DEFAULT NULL,
          PRIMARY KEY (`teamid`),
          UNIQUE KEY `uniq_name` (`lid`, `name`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `plcompteams` (
          `seasonid` INT NOT NULL,
          `compid` INT NOT NULL,
          `teamid` INT NOT NULL,
          PRIMARY KEY (`seasonid`, `compid`, `teamid`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;


-- Postponed fixtures (predictions closed until a new kick-off time is set).
CREATE TABLE IF NOT EXISTS `plpostponed` (
          `lid` INT NOT NULL DEFAULT 0,
          `matchid` INT NOT NULL,
          `reason` VARCHAR(80) NOT NULL DEFAULT '',
          `created` DATETIME NOT NULL,
          PRIMARY KEY (`lid`, `matchid`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;
