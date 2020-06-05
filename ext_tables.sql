#
# Table structure of table 'tx_bookmarks_domain_model_bookmark'
#
CREATE TABLE tx_bookmarks_domain_model_bookmark
(
  parent_uid 		varchar(32)       DEFAULT '',
  parent_pid 		varchar(32)       DEFAULT '',
  parent_table 	varchar(128)      DEFAULT '',
  feuser			  int(10) unsigned 	DEFAULT '0'
);

#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
	bookmarks varchar(255) DEFAULT '' NOT NULL,
);
