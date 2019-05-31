#
# Table structure for table 'tx_myquizpoll_question'
#
CREATE TABLE tx_myquizpoll_question (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumtext,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	fe_group varchar(255) DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	title_hide tinyint(3) DEFAULT '0' NOT NULL,
	name text,
	qtype int(11) DEFAULT '0' NOT NULL,
	category int(11) DEFAULT '0',
	answer1 tinytext,
	correct1 tinyint(3) DEFAULT '0' NOT NULL,
	points1 int(11) DEFAULT '0' NOT NULL,
	joker1_1 tinyint(3) DEFAULT '0' NOT NULL,
	joker2_1 int(11) DEFAULT '0' NOT NULL,
	category1 int(11) DEFAULT '0' NOT NULL,
	answer2 tinytext,
	correct2 tinyint(3) DEFAULT '0' NOT NULL,
	points2 int(11) DEFAULT '0' NOT NULL,
	joker1_2 tinyint(3) DEFAULT '0' NOT NULL,
	joker2_2 int(11) DEFAULT '0' NOT NULL,
	category2 int(11) DEFAULT '0' NOT NULL,
	answer3 tinytext,
	correct3 tinyint(3) DEFAULT '0' NOT NULL,
	points3 int(11) DEFAULT '0' NOT NULL,
	joker1_3 tinyint(3) DEFAULT '0' NOT NULL,
	joker2_3 int(11) DEFAULT '0' NOT NULL,
	category3 int(11) DEFAULT '0' NOT NULL,
	answer4 tinytext,
	correct4 tinyint(3) DEFAULT '0' NOT NULL,
	points4 int(11) DEFAULT '0' NOT NULL,
	joker1_4 tinyint(3) DEFAULT '0' NOT NULL,
	joker2_4 int(11) DEFAULT '0' NOT NULL,
	category4 int(11) DEFAULT '0' NOT NULL,
	answer5 tinytext,
	correct5 tinyint(3) DEFAULT '0' NOT NULL,
	points5 int(11) DEFAULT '0' NOT NULL,
	joker1_5 tinyint(3) DEFAULT '0' NOT NULL,
	joker2_5 int(11) DEFAULT '0' NOT NULL,
	category5 int(11) DEFAULT '0' NOT NULL,
	answer6 tinytext,
	correct6 tinyint(3) DEFAULT '0' NOT NULL,
	points6 int(11) DEFAULT '0' NOT NULL,
	joker1_6 tinyint(3) DEFAULT '0' NOT NULL,
	joker2_6 int(11) DEFAULT '0' NOT NULL,
	category6 int(11) DEFAULT '0' NOT NULL,
	explanation text,
	joker3 varchar(255) DEFAULT '' NOT NULL,
	points int(11) DEFAULT '0' NOT NULL,
	category_next int(11) DEFAULT '0' NOT NULL,
	image text,
	alt_text varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid)
);


#
# Table structure for table 'tx_myquizpoll_voting'
#
CREATE TABLE tx_myquizpoll_voting (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumtext,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	answer_no tinyint(4) DEFAULT '0' NOT NULL,
	question_id int(11) DEFAULT '0' NOT NULL,
	foreign_val varchar(255) DEFAULT '' NOT NULL,
	ip varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY my_voting_answer (answer_no),
	KEY my_voting_foreign (foreign_val),
	KEY my_voting_question (question_id)
);


#
# Table structure for table 'tx_myquizpoll_result'
#
CREATE TABLE tx_myquizpoll_result (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumtext,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,
	homepage varchar(255) DEFAULT '' NOT NULL,
	ip varchar(255) DEFAULT '' NOT NULL,
	p_or_a int(11) DEFAULT '0' NOT NULL,
	p_max int(11) DEFAULT '0' NOT NULL,
	percent int(11) DEFAULT '0' NOT NULL,
	o_max int(11) DEFAULT '0' NOT NULL,
	o_percent int(11) DEFAULT '0' NOT NULL,
	qids text,
	cids text,
	fids text,
	sids text,
	joker1 tinyint(3) DEFAULT '0' NOT NULL,
	joker2 tinyint(3) DEFAULT '0' NOT NULL,
	joker3 tinyint(3) DEFAULT '0' NOT NULL,
	firsttime int(11) DEFAULT '0' NOT NULL,
	lasttime int(11) DEFAULT '0' NOT NULL,
	lastcat int(11) DEFAULT '0' NOT NULL,
	nextcat int(11) DEFAULT '0' NOT NULL,
	fe_uid int(11) DEFAULT '0' NOT NULL,
	start_uid int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);
# CREATE INDEX my_res_qids_index ON tx_myquizpoll_result (qids);
# CREATE INDEX my_res_fids_index ON tx_myquizpoll_result (fids);


#
# Table structure for table 'tx_myquizpoll_relation'
#
CREATE TABLE tx_myquizpoll_relation (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumtext,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	user_id int(11) DEFAULT '0' NOT NULL,
	question_id int(11) DEFAULT '0' NOT NULL,
	textinput text,
	checked1 tinyint(3) DEFAULT '0' NOT NULL,
	checked2 tinyint(3) DEFAULT '0' NOT NULL,
	checked3 tinyint(3) DEFAULT '0' NOT NULL,
	checked4 tinyint(3) DEFAULT '0' NOT NULL,
	checked5 tinyint(3) DEFAULT '0' NOT NULL,
	checked6 tinyint(3) DEFAULT '0' NOT NULL,
	points int(11) DEFAULT '0' NOT NULL,
	nextcat int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY my_user (user_id),
	KEY my_question (question_id)
);
# CREATE INDEX my_rel_user_index ON tx_myquizpoll_relation (user_id);
# CREATE INDEX my_rel_quest_index ON tx_myquizpoll_relation (question_id);



#
# Table structure for table 'tx_myquizpoll_category'
#
CREATE TABLE tx_myquizpoll_category (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	notes text,
	celement int(11) DEFAULT '0' NOT NULL,
	pagetime int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);
