
CREATE TABLE `prefix_lstest` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `testsid` int(10) NOT NULL default '0',
  `intro` text NOT NULL,
  `timecreated` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE `prefix_lstest_tests` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `lang` varchar(10) NOT NULL default '',
  `available` int(4) unsigned NOT NULL default '0',
  `redoallowed` int(4) unsigned NOT NULL default '0',
  `multipleanswer` int(4) unsigned NOT NULL default '0',
  `notansweredquestion` int(4) unsigned NOT NULL default '0',
  `styledefined` int(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE `prefix_lstest_styles` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `testsid` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE `prefix_lstest_levels` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `testsid` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE `prefix_lstest_thresholds` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `stylesid` int(10) unsigned NOT NULL default '0',
  `levelsid` int(10) unsigned NOT NULL default '0',
  `infthreshold` int(4) NOT NULL default '0',
  `supthreshold` int(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE `prefix_lstest_answers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `testsid` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE `prefix_lstest_items` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `testsid` int(10) unsigned NOT NULL default '0',
  `stylesid` int(10) unsigned NOT NULL default '0',
  `question` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE `prefix_lstest_scores` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `itemsid` int(10) unsigned NOT NULL default '0',
  `answersid` int(10) unsigned NOT NULL default '0',
  `nocheckedscore` int(4) NOT NULL default '0',
  `checkedscore` int(4) NOT NULL default '0',
  `stylesid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE `prefix_lstest_user_scores` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `time` int(10) unsigned default NULL,
  `userid` int(10) unsigned NOT NULL default '0',
  `stylesid` int(10) unsigned NOT NULL default '0',
  `levelsid` int(10) unsigned NOT NULL default '0',
  `score` int(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE `prefix_lstest_user_answers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `time` int(10) unsigned default NULL,
  `userid` int(10) unsigned NOT NULL default '0',
  `itemsid` int(10) unsigned NOT NULL default '0',
  `answersid` int(10) unsigned NOT NULL default '0',
  `checked` int(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
# --------------------------------------------------------

