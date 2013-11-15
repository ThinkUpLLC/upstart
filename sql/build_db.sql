-- --------------------------------------------------------

--
-- Table structure for table 'authorizations'
--

CREATE TABLE authorizations (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID.',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time of transaction.',
  token_id varchar(100) NOT NULL COMMENT 'Token ID of transaction.',
  amount int(11) NOT NULL COMMENT 'Monetary amount of transaction in US Dollars.',
  status_code varchar(2) NOT NULL COMMENT 'The status of the transaction request.',
  error_message varchar(255) DEFAULT NULL COMMENT 'Human readable message that specifies the reason for a request failure (optional).',
  payment_method_expiry varchar(10) DEFAULT NULL COMMENT 'Payment method expiration date (optional).',
  caller_reference varchar(20) NOT NULL COMMENT 'Caller reference used for authorization request.',
  recurrence_period varchar(12) NOT NULL DEFAULT '12 Months' COMMENT 'Recurrence period of payment authorization.',
  token_validity_start_date timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Date the token becomes valid.',
  PRIMARY KEY (id),
  UNIQUE KEY token_id (token_id),
  KEY status_code (status_code)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Amazon FPS recurring-use payment authorizations.';

-- --------------------------------------------------------

--
-- Table structure for table 'authorization_status_codes'
--

CREATE TABLE authorization_status_codes (
  `code` varchar(2) NOT NULL COMMENT 'Status code.',
  description varchar(125) NOT NULL COMMENT 'Description.',
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Amazon FPS authorization request status codes.';

-- --------------------------------------------------------

--
-- Table structure for table 'clicks'
--

CREATE TABLE clicks (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID.',
  caller_reference_suffix varchar(20) NOT NULL COMMENT 'Second half of caller reference string.',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time of click.',
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Clicks to the pledge page.';

-- --------------------------------------------------------

--
-- Table structure for table 'error_log'
--

CREATE TABLE error_log (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID.',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time error was thrown.',
  commit_hash varchar(50) NOT NULL COMMENT 'Git commit hash of code tree.',
  filename varchar(255) NOT NULL COMMENT 'Filename of the code where the error was thrown.',
  line_number int(11) NOT NULL COMMENT 'Line number in the code where the error was thrown.',
  method varchar(100) NOT NULL COMMENT 'Method where the error was thrown.',
  debug text NOT NULL COMMENT 'Debugging info.',
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Log of user errors.';

-- --------------------------------------------------------

--
-- Table structure for table 'install_log'
--

CREATE TABLE install_log (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID.',
  subscriber_id int(11) NOT NULL COMMENT 'Subscriber ID.',
  commit_hash varchar(41) NOT NULL COMMENT 'Git commit hash of installation version.',
  migration_success tinyint(4) NOT NULL COMMENT 'Whether or not install/upgrade was successful.',
  migration_message text NOT NULL COMMENT 'Install/upgrade debug message.',
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Installation upgrade log.';

-- --------------------------------------------------------

--
-- Table structure for table 'subscribers'
--

CREATE TABLE subscribers (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID.',
  email varchar(200) NOT NULL COMMENT 'Subscriber email address.',
  pwd varchar(255) NOT NULL COMMENT 'Subscriber password.',
  pwd_salt varchar(255) NOT NULL COMMENT 'Subscriber password salt.',
  creation_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time of subscription.',
  network_user_id varchar(30) DEFAULT NULL COMMENT 'Subscriber''s network user ID.',
  network_user_name varchar(255) NOT NULL COMMENT 'Subscriber''s network username.',
  network varchar(20) DEFAULT NULL COMMENT 'Subscriber''s authorized network, ie, Twitter or Facebook.',
  full_name varchar(255) NOT NULL COMMENT 'Subscriber''s full name (as specified on network).',
  follower_count int(11) NOT NULL COMMENT 'Follower or subscriber count of service user.',
  is_verified int(1) NOT NULL COMMENT 'Whether or not the service user is verified.',
  oauth_access_token varchar(255) NOT NULL COMMENT 'OAuth access token for network authorization.',
  oauth_access_token_secret varchar(255) NOT NULL COMMENT 'OAuth secret access token for network authorization.',
  verification_code int(10) NOT NULL COMMENT 'Code for verifying email address.',
  is_email_verified int(1) NOT NULL COMMENT 'Whether or not email address has been verified, 1 or 0.',
  is_from_waitlist tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Whether or not subscriber was on waitlist (1 if so, 0 if not).',
  membership_level varchar(20) NOT NULL DEFAULT '' COMMENT 'Subscriber''s membership level (Member, Pro, Exec, Early Bird, etc).',
  thinkup_username varchar(50) DEFAULT NULL COMMENT 'ThinkUp username.',
  date_installed timestamp NULL DEFAULT NULL COMMENT 'Installation start time.',
  session_api_token varchar(64) DEFAULT NULL COMMENT 'API token for authorizing on installation.',
  last_dispatched timestamp NULL DEFAULT NULL COMMENT 'Last time this installation was dispatched for crawl.',
  commit_hash varchar(41) DEFAULT NULL COMMENT 'Git commit hash of installation version.',
  is_installation_active tinyint(1) DEFAULT NULL COMMENT 'Whether or not the installation is active.',
  PRIMARY KEY (id),
  UNIQUE KEY email (email),
  UNIQUE KEY network_user_id (network_user_id,network),
  UNIQUE KEY thinkup_username (thinkup_username)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Paid subscribers who have authorized their social network ac';

-- --------------------------------------------------------

--
-- Table structure for table 'subscriber_archive'
--

CREATE TABLE subscriber_archive (
  email varchar(200) NOT NULL COMMENT 'Subscriber email address.',
  pwd varchar(255) NOT NULL COMMENT 'Subscriber password.',
  pwd_salt varchar(255) NOT NULL COMMENT 'Subscriber password salt.',
  creation_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time of subscription.',
  network_user_id varchar(30) DEFAULT NULL COMMENT 'Subscriber''s network user ID.',
  network_user_name varchar(255) NOT NULL COMMENT 'Subscriber''s network username.',
  network varchar(20) DEFAULT NULL COMMENT 'Subscriber''s authorized network, ie, Twitter or Facebook.',
  full_name varchar(255) NOT NULL COMMENT 'Subscriber''s full name (as specified on network).',
  follower_count int(11) NOT NULL COMMENT 'Follower or subscriber count of service user.',
  is_verified int(1) NOT NULL COMMENT 'Whether or not the service user is verified.',
  oauth_access_token varchar(255) NOT NULL COMMENT 'OAuth access token for network authorization.',
  oauth_access_token_secret varchar(255) NOT NULL COMMENT 'OAuth secret access token for network authorization.',
  verification_code int(10) NOT NULL COMMENT 'Code for verifying email address.',
  is_email_verified int(1) NOT NULL COMMENT 'Whether or not email address has been verified, 1 or 0.',
  is_from_waitlist tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Whether or not subscriber was on waitlist (1 if so, 0 if not).',
  membership_level varchar(20) NOT NULL DEFAULT '' COMMENT 'Subscriber''s membership level (Member, Pro, Exec, Early Bird, etc).',
  thinkup_username varchar(50) DEFAULT NULL COMMENT 'ThinkUp username.',
  date_installed timestamp NULL DEFAULT NULL COMMENT 'Installation start time.',
  session_api_token varchar(64) DEFAULT NULL COMMENT 'API token for authorizing on installation.',
  last_dispatched timestamp NULL DEFAULT NULL COMMENT 'Last time this installation was dispatched for crawl.',
  commit_hash varchar(41) DEFAULT NULL COMMENT 'Git commit hash of installation version.',
  is_installation_active tinyint(1) DEFAULT NULL COMMENT 'Whether or not the installation is active.',
  token_id varchar(100) NOT NULL COMMENT 'Token ID of payment authorization.',
  amount int(11) NOT NULL COMMENT 'Monetary amount of payment authorization in US Dollars.',
  status_code varchar(2) NOT NULL COMMENT 'The status of the payment authorization.',
  error_message varchar(255) DEFAULT NULL COMMENT 'Human readable message that specifies the reason for a request failure (optional).',
  payment_method_expiry varchar(10) DEFAULT NULL COMMENT 'Payment method expiration date (optional).',
  caller_reference varchar(20) NOT NULL COMMENT 'Caller reference used for authorization request.',
  recurrence_period varchar(12) NOT NULL DEFAULT '12 Months' COMMENT 'Recurrence period of payment authorization.',
  token_validity_start_date timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Date the token becomes valid.'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Deleted subscribers with authorizaton data.';

-- --------------------------------------------------------

--
-- Table structure for table 'subscriber_authorizations'
--

CREATE TABLE subscriber_authorizations (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID.',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Time transaction was recorded.',
  subscriber_id int(11) NOT NULL COMMENT 'Subscriber ID keyed to subscribers.',
  authorization_id int(11) NOT NULL COMMENT 'Authorization ID keyed to authorizations.',
  PRIMARY KEY (id),
  UNIQUE KEY subscriber_id (subscriber_id,authorization_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Payment authorizations by known subscribers.';

