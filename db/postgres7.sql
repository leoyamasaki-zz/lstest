
CREATE TABLE prefix_lstest (
  id SERIAL PRIMARY KEY,
  course integer  NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  testsid integer NOT NULL default '0',
  intro text NOT NULL,
  timecreated integer  NOT NULL default '0',
  timemodified integer  NOT NULL default '0'
);
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE prefix_lstest_tests (
  id SERIAL PRIMARY KEY,
  name varchar(255) NOT NULL default '',
  lang varchar(10) NOT NULL default '',
  available integer  NOT NULL default '0',
  redoallowed integer  NOT NULL default '0',
  multipleanswer integer  NOT NULL default '0',
  notansweredquestion integer  NOT NULL default '0',
  styledefined integer  NOT NULL default '0'
);
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE prefix_lstest_styles (
  id SERIAL PRIMARY KEY,
  testsid integer  NOT NULL default '0',
  name varchar(255) NOT NULL default ''
);
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE prefix_lstest_levels (
  id SERIAL PRIMARY KEY,
  testsid integer  NOT NULL default '0',
  name varchar(255) NOT NULL default ''
);
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE prefix_lstest_thresholds (
  id SERIAL PRIMARY KEY,
  stylesid integer  NOT NULL default '0',
  levelsid integer  NOT NULL default '0',
  infthreshold integer NOT NULL default '0',
  supthreshold integer NOT NULL default '0'
);
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE prefix_lstest_answers (
  id SERIAL PRIMARY KEY,
  testsid integer  NOT NULL default '0',
  name varchar(255) NOT NULL default ''
);
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE prefix_lstest_items (
  id SERIAL PRIMARY KEY,
  testsid integer  NOT NULL default '0',
  stylesid integer  NOT NULL default '0',
  question varchar(1024) NOT NULL default ''
);
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE prefix_lstest_scores (
  id SERIAL PRIMARY KEY,
  itemsid integer  NOT NULL default '0',
  answersid integer  NOT NULL default '0',
  nocheckedscore integer NOT NULL default '0',
  checkedscore integer NOT NULL default '0'
  stylesid integer NOT NULL default '0'
);
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE prefix_lstest_user_scores (
  id SERIAL PRIMARY KEY,
  time integer  default NULL,
  userid integer  NOT NULL default '0',
  stylesid integer  NOT NULL default '0',
  levelsid integer  NOT NULL default '0',
  score integer  NOT NULL default '0'
);
# --------------------------------------------------------
# --------------------------------------------------------
CREATE TABLE prefix_lstest_user_answers (
  id SERIAL PRIMARY KEY,
  time integer  default NULL,
  userid integer  NOT NULL default '0',
  itemsid integer  NOT NULL default '0',
  answersid integer  NOT NULL default '0',
  checked integer  NOT NULL default '0'
);
# --------------------------------------------------------
