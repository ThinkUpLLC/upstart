CREATE TABLE  clicks (
id INT( 11 ) NOT NULL AUTO_INCREMENT COMMENT  'Internal unique ID.',
timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT  'Time of click.',
PRIMARY KEY (  id )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT =  'Clicks to the pledge page.';

CREATE TABLE  transactions (
id INT NOT NULL AUTO_INCREMENT COMMENT  'Internal unique ID.',
timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT  'Time of transaction.',
token_id VARCHAR( 100 ) NOT NULL COMMENT  'Token ID of transaction.',
amount INT NOT NULL COMMENT  'Monetary amount of transaction in US Dollars.',
expiry DATE NOT NULL COMMENT  'Expiration date of transaction.',
PRIMARY KEY (  id )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT =  'Amazon Flexible Payment System transactions.';

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
