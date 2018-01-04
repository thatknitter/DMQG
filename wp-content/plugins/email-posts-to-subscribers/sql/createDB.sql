CREATE TABLE IF NOT EXISTS elp_templatetable (
  elp_templ_id INT unsigned NOT NULL AUTO_INCREMENT,
  elp_templ_heading VARCHAR(255) NOT NULL,
  elp_templ_header TEXT NULL,
  elp_templ_body TEXT NULL,
  elp_templ_footer TEXT NULL,
  elp_templ_status VARCHAR(25) NOT NULL default 'Dynamic',
  elp_email_type VARCHAR(100) NOT NULL default 'System',
  PRIMARY KEY  (elp_templ_id)
) ENGINE=MyISAM /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- SQLQUERY ---

CREATE TABLE IF NOT EXISTS elp_emaillist (
  elp_email_id INT unsigned NOT NULL AUTO_INCREMENT,
  elp_email_name VARCHAR(255) NOT NULL,
  elp_email_mail VARCHAR(255) NOT NULL,
  elp_email_status VARCHAR(25) NOT NULL default 'Unconfirmed',
  elp_email_created datetime NOT NULL default '0000-00-00 00:00:00',
  elp_email_viewcount VARCHAR(100) NOT NULL,
  elp_email_group VARCHAR(100) NOT NULL default 'Public',
  elp_email_guid VARCHAR(255) NOT NULL,
  PRIMARY KEY  (elp_email_id)
) ENGINE=MyISAM /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- SQLQUERY ---

CREATE TABLE IF NOT EXISTS elp_sendsetting (
  elp_set_id INT unsigned NOT NULL AUTO_INCREMENT,
  elp_set_guid VARCHAR(255) NOT NULL,
  elp_set_name VARCHAR(255) NOT NULL,
  elp_set_templid VARCHAR(255) NOT NULL,
  elp_set_totalsent INT unsigned NOT NULL,
  elp_set_unsubscribelink VARCHAR(10) NOT NULL,
  elp_set_viewstatus VARCHAR(10) NOT NULL,
  elp_set_postcount INT unsigned NOT NULL,
  elp_set_postcategory VARCHAR(225) NOT NULL,
  elp_set_postorderby VARCHAR(10) NOT NULL,
  elp_set_postorder VARCHAR(10) NOT NULL,
  elp_set_scheduleday VARCHAR(50) NOT NULL default '#0# -- #1# -- #2# -- #3# -- #4# -- #5# -- #6#',
  elp_set_scheduletime time NOT NULL default '12:00:00',
  elp_set_scheduletype VARCHAR(20) NOT NULL default 'Cron',
  elp_set_lastschedulerun datetime NOT NULL default '0000-00-00 00:00:00',
  elp_set_status VARCHAR(10) NOT NULL default 'On',
  elp_set_emaillistgroup VARCHAR(225) NOT NULL default 'Public',
  PRIMARY KEY  (elp_set_id)
) ENGINE=MyISAM /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- SQLQUERY ---

CREATE TABLE IF NOT EXISTS elp_sentdetails (
  elp_sent_id INT unsigned NOT NULL AUTO_INCREMENT,
  elp_sent_guid VARCHAR(255) NOT NULL,
  elp_sent_qstring VARCHAR(255) NOT NULL,
  elp_sent_source VARCHAR(255) NOT NULL,
  elp_sent_starttime datetime NOT NULL default '0000-00-00 00:00:00',
  elp_sent_endtime datetime NOT NULL default '0000-00-00 00:00:00',
  elp_sent_count INT unsigned NOT NULL,
  elp_sent_preview TEXT NULL,
  elp_sent_status VARCHAR(25) NOT NULL default 'Sent',
  elp_sent_type VARCHAR(25) NOT NULL default 'Instant Mail',
  elp_sent_subject VARCHAR(255) NOT NULL,
  PRIMARY KEY  (elp_sent_id)
) ENGINE=MyISAM /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- SQLQUERY ---

CREATE TABLE IF NOT EXISTS elp_deliverreport (
  elp_deliver_id INT unsigned NOT NULL AUTO_INCREMENT,
  elp_deliver_sentguid VARCHAR(255) NOT NULL,
  elp_deliver_emailid INT unsigned NOT NULL,
  elp_deliver_emailmail VARCHAR(255) NOT NULL,
  elp_deliver_sentdate datetime NOT NULL default '0000-00-00 00:00:00',
  elp_deliver_status VARCHAR(25) NOT NULL,
  elp_deliver_viewdate datetime NOT NULL default '0000-00-00 00:00:00',
  elp_deliver_sentstatus VARCHAR(25) NOT NULL default 'Sent',
  elp_deliver_senttype VARCHAR(25) NOT NULL default 'Instant Mail',
  PRIMARY KEY  (elp_deliver_id)
) ENGINE=MyISAM /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- SQLQUERY ---

CREATE TABLE IF NOT EXISTS elp_pluginconfig (
  elp_c_id INT unsigned NOT NULL AUTO_INCREMENT,
  elp_c_fromname VARCHAR(255) NOT NULL,
  elp_c_fromemail VARCHAR(255) NOT NULL,
  elp_c_mailtype VARCHAR(255) NOT NULL,
  elp_c_adminmailoption VARCHAR(255) NOT NULL,
  elp_c_adminemail VARCHAR(255) NOT NULL,
  elp_c_adminmailsubject VARCHAR(255) NOT NULL,
  elp_c_adminmailcontant TEXT NULL,
  elp_c_usermailoption VARCHAR(255) NOT NULL,
  elp_c_usermailsubject VARCHAR(255) NOT NULL,
  elp_c_usermailcontant TEXT NULL,
  elp_c_optinoption VARCHAR(255) NOT NULL,
  elp_c_optinsubject VARCHAR(255) NOT NULL,
  elp_c_optincontent TEXT NULL,
  elp_c_optinlink VARCHAR(255) NOT NULL,
  elp_c_unsublink  VARCHAR(255) NOT NULL,
  elp_c_unsubtext TEXT NULL,
  elp_c_unsubhtml TEXT NULL,
  elp_c_subhtml TEXT NULL,
  elp_c_message1 TEXT NULL,
  elp_c_message2 TEXT NULL,
  PRIMARY KEY  (elp_c_id)
) ENGINE=MyISAM /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- SQLQUERY ---

CREATE TABLE IF NOT EXISTS elp_postnotification (
  elp_note_id INT unsigned NOT NULL AUTO_INCREMENT,
  elp_note_guid VARCHAR(255) NOT NULL,
  elp_note_postcat VARCHAR(255) NOT NULL,
  elp_note_emailgroup VARCHAR(255) NOT NULL,
  elp_note_mailsubject VARCHAR(255) NOT NULL,
  elp_note_mailcontent TEXT NULL,
  elp_note_status VARCHAR(100) NOT NULL default 'Enable',
  elp_note_type VARCHAR(100) NOT NULL default 'Notification',
  PRIMARY KEY  (elp_note_id)
) ENGINE=MyISAM /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;