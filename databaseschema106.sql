# MySQL dump 8.16
#
# Host: localhost    Database: predictionleague106
#--------------------------------------------------------
# Server version	3.23.45

#
# Table structure for table 'plconfigdata'
#

CREATE TABLE plconfigdata (
  lid int(11) NOT NULL default '0',
  grp int(11) NOT NULL default '0',
  param varchar(32) NOT NULL default '',
  value varchar(32) NOT NULL default '',
  descr text NOT NULL,
  ro enum('Y','N') default 'N',
  PRIMARY KEY  (lid,param)
) TYPE=MyISAM;

#
# Table structure for table 'plmatchdata'
#

CREATE TABLE plmatchdata (
  lid int(11) NOT NULL default '0',
  matchid int(11) NOT NULL auto_increment,
  matchdate datetime NOT NULL default '0000-00-00 00:00:00',
  hometeam varchar(30) NOT NULL default '',
  awayteam varchar(30) NOT NULL default '',
  homescore smallint(5) unsigned default NULL,
  awayscore smallint(5) unsigned default NULL,
  PRIMARY KEY  (lid,matchid)
) TYPE=MyISAM;

#
# Table structure for table 'plmsgdata'
#

CREATE TABLE plmsgdata (
  lid int(11) NOT NULL default '0',
  msgid int(11) NOT NULL auto_increment,
  threadid int(11) default NULL,
  prevmsg int(11) default NULL,
  sender int(11) NOT NULL default '0',
  receiver int(11) NOT NULL default '0',
  subject varchar(255) default NULL,
  body text,
  status enum('new','read','deleted') default NULL,
  msgtime timestamp(14) NOT NULL,
  PRIMARY KEY  (lid,msgid)
) TYPE=MyISAM;

#
# Table structure for table 'plpredictiondata'
#

CREATE TABLE plpredictiondata (
  lid int(11) NOT NULL default '0',
  userid int(11) NOT NULL default '0',
  matchid int(11) NOT NULL default '0',
  predtime timestamp(14) NOT NULL,
  homescore smallint(5) unsigned default NULL,
  awayscore smallint(5) unsigned default NULL,
  PRIMARY KEY  (lid,userid,matchid)
) TYPE=MyISAM;

#
# Table structure for table 'plstandings'
#

CREATE TABLE plstandings (
  lid int(11) NOT NULL default '0',
  userid int(11) NOT NULL default '0',
  position smallint(6) NOT NULL default '0',
  pld int(10) unsigned default NULL,
  won int(10) unsigned default NULL,
  drawn int(10) unsigned default NULL,
  lost int(10) unsigned default NULL,
  gfor smallint(5) unsigned default NULL,
  gagainst smallint(5) unsigned default NULL,
  diff smallint(6) default NULL,
  points int(10) unsigned default NULL,
  PRIMARY KEY  (lid,userid)
) TYPE=MyISAM;

#
# Table structure for table 'pluserdata'
#

CREATE TABLE pluserdata (
  lid int(11) NOT NULL default '0',
  userid int(11) NOT NULL auto_increment,
  username varchar(32) NOT NULL default '',
  password varchar(40) default NULL,
  email varchar(60) default NULL,
  icon varchar(128) default NULL,
  lang varchar(32) default NULL,
  usertype smallint(6) default NULL,
  dflths smallint(6) default '0',
  dfltas smallint(6) default '0',
  since date default NULL,
  PRIMARY KEY  (lid,userid)
) TYPE=MyISAM;

