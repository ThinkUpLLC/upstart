CREATE TABLE install_log (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID.',
  user_route_id int(11) NOT NULL COMMENT 'User route ID.',
  commit_hash varchar(41) NOT NULL COMMENT 'Git commit hash of installation version.',
  migration_success tinyint(4) NOT NULL COMMENT 'Whether or not install/upgrade was successful.',
  migration_message text NOT NULL COMMENT 'Install/upgrade debug message.',
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Installation upgrade log.';

CREATE TABLE user_routes (
  id int(11) NOT NULL AUTO_INCREMENT,
  email varchar(70) NOT NULL,
  route varchar(100) NOT NULL,
  twitter_username varchar(255) NOT NULL,
  twitter_user_id varchar(30) NOT NULL,
  full_name varchar(255) NOT NULL,
  oauth_access_token varchar(255) NOT NULL,
  oauth_access_token_secret varchar(255) NOT NULL,
  is_verified tinyint(1) NOT NULL,
  follower_count int(11) NOT NULL,
  date_waitlisted timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp when user was inserted onto list.',
  database_name varchar(70) NOT NULL COMMENT 'Name of the database associated with this route.',
  is_active tinyint(1) NOT NULL COMMENT 'Whether or not the route is active.',
  last_dispatched timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last time this user route was dispatched for crawl.',
  commit_hash varchar(41) NOT NULL COMMENT 'Git commit hash of installation version.',
  PRIMARY KEY (id),
  UNIQUE KEY email (email,twitter_user_id),
  KEY commit_hash (commit_hash)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Route users to their installation.';

CREATE TABLE clicks (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID.',
  caller_reference_suffix varchar(20) NOT NULL COMMENT 'Second half of caller reference string.',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time of click.',
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Clicks to the pledge page.';

CREATE TABLE transactions (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID.',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time of transaction.',
  token_id varchar(100) NOT NULL COMMENT 'Token ID of transaction.',
  amount int(11) NOT NULL COMMENT 'Monetary amount of transaction in US Dollars.',
  status_code varchar(2) NOT NULL COMMENT 'The status of the transaction request.',
  error_message varchar(255) DEFAULT NULL COMMENT 'Human readable message that specifies the reason for a request failure (optional).',
  payment_method_expiry varchar(10) DEFAULT NULL COMMENT 'Payment method expiration date (optional).',
  PRIMARY KEY (id),
  UNIQUE KEY token_id (token_id),
  KEY status_code (status_code)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Amazon Flexible Payment System transactions.';

CREATE TABLE subscribers (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID.',
  email varchar(200) NOT NULL COMMENT 'Subscriber email address.',
  creation_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time of subscription.',
  network_user_id varchar(30) NOT NULL COMMENT 'Subscriber''s network user ID.',
  network_user_name varchar(255) NOT NULL COMMENT 'Subscriber''s network username.',
  network varchar(20) NOT NULL COMMENT 'Subscriber''s authorized network, ie, Twitter or Facebook.',
  full_name varchar(255) NOT NULL COMMENT 'Subscriber''s full name (as specified on network).',
  oauth_access_token varchar(255) NOT NULL COMMENT 'OAuth access token for network authorization.',
  oauth_access_token_secret varchar(255) NOT NULL COMMENT 'OAuth secret access token for network authorization.',
  verification_code int(10) NOT NULL COMMENT 'Code for verifying email address.',
  is_email_verified int(1) NOT NULL COMMENT 'Whether or not email address has been verified, 1 or 0.',
  PRIMARY KEY (id),
  UNIQUE KEY email (email)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Paid subscribers who have authorized their social network ac';

CREATE TABLE  subscriber_transactions (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT  'Internal unique ID.',
timestamp TIMESTAMP NOT NULL COMMENT  'Time transaction was recorded.',
subscriber_id INT NOT NULL COMMENT  'Subscriber ID keyed to subscribers.',
transaction_id INT NOT NULL COMMENT  'Transaction ID keyed to transactions.'
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT =  'Transactions by known subscribers.';

CREATE TABLE  transaction_status_codes (
code VARCHAR( 2 ) NOT NULL COMMENT  'Status code.',
description VARCHAR( 125 ) NOT NULL COMMENT  'Description.',
PRIMARY KEY (  code )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT =  'Amazon Flexible Payment System transaction status codes.';


INSERT INTO  transaction_status_codes ( code , description )
VALUES (
'SA',  'Success status for the ABT payment method.'
), (
'SB',  'Success status for the ACH (bank account) payment method.'
), (
'SC',  'Success status for the credit card payment method.'
), (
'SE',  'System error.'
), (
'A',  'Buyer abandoned the pipeline.'
), (
'CE',  'Specifies a caller exception.'
), (
'PE',  'Payment Method Mismatch Error: Specifies that the buyer does not have payment method that you have requested.'
), (
'NP',  'This account type does not support the specified payment method.'
), (
'NM',  'You are not registered as a third-party caller to make this transaction. Contact Amazon Payments for more information.'
);
