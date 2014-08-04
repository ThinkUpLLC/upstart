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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Amazon FPS recurring-use payment authorizations.';

-- --------------------------------------------------------

--
-- Table structure for table 'subscription_status_codes'
--

CREATE TABLE subscription_status_codes (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Log of user errors.';

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
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time of log entry.',
  PRIMARY KEY (id),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Installation upgrade log.';

-- --------------------------------------------------------

--
-- Table structure for table 'payments'
--

CREATE TABLE payments (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID.',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time of transaction.',
  transaction_id varchar(100) NOT NULL COMMENT 'Transaction ID of payment from Amazon.',
  request_id varchar(100) NOT NULL COMMENT 'Request ID of transaction, assigned by Amazon.',
  transaction_status varchar(20) NOT NULL COMMENT 'The status of the payment request.',
  status_message varchar(255) DEFAULT NULL COMMENT 'Human readable message that specifies the reason for a request failure (optional).',
  amount int(11) DEFAULT NULL COMMENT 'Amount of payment in USD.',
  caller_reference varchar(24) DEFAULT NULL COMMENT 'Caller reference used for charge request.',
  PRIMARY KEY (id),
  KEY transaction_status (transaction_status)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Amazon FPS payment capture transactions';

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
  is_from_waitlist int(1) NOT NULL DEFAULT '0' COMMENT 'Whether or not subscriber was on waitlist (1 if so, 0 if not).',
  membership_level varchar(20) NOT NULL DEFAULT '' COMMENT 'Subscriber''s membership level (Member, Pro, Exec, Early Bird, etc).',
  is_membership_complimentary int(1) NOT NULL DEFAULT '0' COMMENT 'Whether or not the membership is complimentary, ie, free/not paid for.',
  thinkup_username varchar(50) DEFAULT NULL COMMENT 'ThinkUp username.',
  date_installed timestamp NULL DEFAULT NULL COMMENT 'Installation start time.',
  api_key_private varchar(32) DEFAULT NULL COMMENT 'API key for authorizing on installation.',
  last_dispatched timestamp NULL DEFAULT NULL COMMENT 'Last time this installation was dispatched for crawl.',
  commit_hash varchar(41) DEFAULT NULL COMMENT 'Git commit hash of installation version.',
  is_installation_active int(1) DEFAULT NULL COMMENT 'Whether or not the installation is active.',
  last_login date DEFAULT NULL COMMENT 'Last time member logged in.',
  failed_logins int(11) NOT NULL DEFAULT '0' COMMENT 'Current number of failed login attempts.',
  account_status varchar(150) NOT NULL DEFAULT '' COMMENT 'Description of account status, i.e., "Inactive due to excessive failed login attempts".',
  is_activated int(1) NOT NULL DEFAULT '0' COMMENT 'If user is activated, 1 for true, 0 for false.',
  password_token varchar(64) DEFAULT NULL COMMENT 'Password reset token.',
  timezone varchar(50) NOT NULL DEFAULT 'UTC' COMMENT 'Subscriber timezone.',
  subscription_status varchar(50) DEFAULT 'Free trial' COMMENT 'Status of subscription payment.',
  subscription_recurrence varchar(10) NOT NULL DEFAULT '1 month' COMMENT 'How often subscription renews, 1 month or 12 months.',
  total_payment_reminders_sent int(11) NOT NULL DEFAULT '0' COMMENT 'The number of payment reminder emails sent to this subscriber.',
  payment_reminder_last_sent timestamp NULL DEFAULT NULL COMMENT 'Last time a payment reminder was sent to this subscriber.',
  is_account_closed int(1) NOT NULL DEFAULT '0' COMMENT 'Whether or not the member closed their account.',
  PRIMARY KEY (id),
  UNIQUE KEY email (email),
  UNIQUE KEY network_user_id (network_user_id,network),
  UNIQUE KEY thinkup_username (thinkup_username),
  KEY subscription_status (subscription_status),
  KEY payment_reminder_last_sent (payment_reminder_last_sent),
  KEY is_account_closed (is_account_closed)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Paid subscribers who have authorized their social network ac';

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
  token_validity_start_date timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Date the token becomes valid.',
  subscription_status varchar(50) DEFAULT NULL COMMENT 'Status of subscription payment.',
  total_payment_reminders_sent int(11) NOT NULL DEFAULT '0' COMMENT 'The number of payment reminder emails sent to this subscriber.',
  payment_reminder_last_sent timestamp NULL DEFAULT NULL COMMENT 'Last time a payment reminder was sent to this subscriber.',
  is_account_closed int(1) NOT NULL DEFAULT '0' COMMENT 'Whether or not the member closed their account.'
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Payment authorizations by known subscribers.';

-- --------------------------------------------------------

--
-- Table structure for table 'subscriber_payments'
--

CREATE TABLE subscriber_payments (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID.',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Time transaction was recorded.',
  subscriber_id int(11) NOT NULL COMMENT 'Subscriber ID keyed to subscribers.',
  payment_id int(11) NOT NULL COMMENT 'Payment ID keyed to payments.',
  PRIMARY KEY (id),
  KEY subscriber_id (subscriber_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Payments by known subscribers.';

-- --------------------------------------------------------

--
-- Table structure for table 'subscription_operations'
--

CREATE TABLE subscription_operations (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID.',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of insertion.',
  subscriber_id int(11) NOT NULL COMMENT 'Subscriber ID.',
  operation varchar(25) NOT NULL COMMENT 'Operation performed on Amazon.',
  payment_reason varchar(100) NOT NULL COMMENT 'Reason for payment.',
  transaction_amount varchar(100) NOT NULL COMMENT 'Amount of transaction.',
  recurring_frequency varchar(25) NOT NULL COMMENT 'How often subscription recurs, 1 month or 12 months.',
  status_code varchar(2) NOT NULL COMMENT 'Transaction status code.',
  buyer_email varchar(255) NOT NULL COMMENT 'Amazon''s buyer email address.',
  reference_id varchar(20) NOT NULL COMMENT 'Caller reference for transaction.',
  amazon_subscription_id varchar(100) NOT NULL COMMENT 'Amazon''s subscription ID.',
  transaction_date timestamp NOT NULL COMMENT 'Amazon''s transaction date.',
  buyer_name varchar(255) NOT NULL COMMENT 'Amazon''s buyer name.',
  payment_method varchar(25) NOT NULL COMMENT 'Payment method.',
  PRIMARY KEY (id),
  KEY subscriber_id (subscriber_id),
  UNIQUE KEY amazon_subscription_id (amazon_subscription_id,reference_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Amazon subscription operations.';


-- Populate subscription status codes

INSERT INTO  subscription_status_codes ( code , description )
VALUES (
'SA',  'Success status for the ABT payment method.'
), (
'SS',  'The subscription was completed.'
), (
'SF',  'The subscription failed.'
), (
'SI',  'The subscription has initiated.'
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
