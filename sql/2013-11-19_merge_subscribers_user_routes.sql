--
-- Subscribers
-- Add permanent new fields
--
ALTER TABLE  subscribers ADD  is_from_waitlist TINYINT NOT NULL DEFAULT  '0' 
COMMENT  'Whether or not subscriber was on waitlist (1 if so, 0 if not).';

ALTER TABLE  subscribers ADD  membership_level VARCHAR( 20 ) NOT NULL DEFAULT '' 
COMMENT  'Subscriber''s membership level (Member, Pro, Exec, Early Bird, etc).';

--
-- Set value of membership_level field based on authorization amount
--
UPDATE subscribers s INNER JOIN subscriber_authorizations sa ON sa.subscriber_id = s.id 
INNER JOIN authorizations a ON sa.authorization_id = a.id 
SET s.membership_level = if(a.amount = 60, 'Member', if(a.amount = 120, 'Pro', 
if(a.amount = 996, 'Exec', if(a.amount = 50 and sa.timestamp < '2013-11-01 12:00:00' , 'Early Bird', 
if(a.amount = 50 and sa.timestamp > '2013-11-01 12:00:00' , 'Late Bird', s.membership_level)))));

--
-- Move user_routes data over to subscribers_merged
--
--
-- Create temporary table field we'll use for merge
--
CREATE TABLE subscribers_merged LIKE subscribers;

ALTER TABLE  subscribers_merged 
ADD thinkup_username VARCHAR( 50 ) NULL COMMENT  'ThinkUp username.',
ADD date_installed TIMESTAMP NULL DEFAULT NULL  COMMENT  'Installation start time.',
ADD session_api_token VARCHAR( 64 ) NULL COMMENT  'API token for authorizing on installation.',
ADD last_dispatched TIMESTAMP NULL DEFAULT NULL COMMENT  'Last time this installation was dispatched for crawl.',
ADD commit_hash VARCHAR( 41 ) NULL COMMENT  'Git commit hash of installation version.',
ADD is_installation_active TINYINT( 1 ) NULL DEFAULT NULL COMMENT  'Whether or not the installation is active.',
ADD UNIQUE ( thinkup_username );

INSERT INTO subscribers_merged  SELECT *, null, null, null, null, null, 0 FROM subscribers;

--
-- Create temporary field we'll use for joining during INSERT/UPDATE
--
ALTER TABLE subscribers_merged ADD  user_route_id INT NOT NULL COMMENT  'Temporary field.';

--
-- Set subscribers_merged.is_from_waitlist and user_route_id field for email addresses already in user_routes
--
UPDATE subscribers_merged s INNER JOIN user_routes ur ON ur.email = s.email 
SET s.is_from_waitlist = 1, s.user_route_id = ur.id, s.is_installation_active = ur.is_active,
s.commit_hash = ur.commit_hash, s.date_installed = ur.date_waitlisted, s.last_dispatched = ur.last_dispatched;

--
-- Insert rows from user_routes into subscribers_merged and ignore duplicate keys
--
INSERT IGNORE INTO subscribers_merged (email, pwd, pwd_salt, creation_time, network_user_id, network_user_name, 
network, full_name, follower_count, is_verified, oauth_access_token, oauth_access_token_secret, verification_code, 
is_email_verified, is_from_waitlist, membership_level, thinkup_username, is_installation_active, user_route_id,
commit_hash, date_installed, last_dispatched) 
SELECT email, '', '', date_waitlisted, twitter_user_id, twitter_username, 'twitter', full_name, follower_count, 
is_verified, oauth_access_token, oauth_access_token_secret, '', 0, 1, 'Waitlist', 
REPLACE(REPLACE(ur.route, 'https://', ''), '.thinkup.com/', ''), is_active, id,
commit_hash, date_waitlisted, last_dispatched 
FROM user_routes ur;


--
-- Install log
-- 
-- Add subscriber_id column
--
ALTER TABLE install_log ADD subscriber_id INT( 11 ) NOT NULL  COMMENT  'Subscriber ID.' AFTER  id;
-- 
-- Set subscriber_id column based on contents of subscribers_merged
--
UPDATE install_log il INNER JOIN subscribers_merged sm ON sm.user_route_id = il.user_route_id  
SET subscriber_id = sm.id;
--
-- Drop user_route_id column
--
ALTER TABLE install_log DROP user_route_id;

-- Drop subscribers_merged temporary user_route_id field
ALTER TABLE subscribers_merged DROP user_route_id;

--
-- Rename new tables (but keep the old ones around just in case)
--
-- Subscribers
RENAME TABLE subscribers TO  _bak_subscribers;
RENAME TABLE subscribers_merged TO  subscribers;
--
-- User routes
--
RENAME TABLE user_routes TO _bak_user_routes;


--
-- Subscriber archive
--
RENAME TABLE subscriber_archive TO  _bak_subscriber_archive;

ALTER TABLE  _bak_subscriber_archive ADD  is_from_waitlist TINYINT NOT NULL DEFAULT  '0' 
COMMENT  'Whether or not subscriber was on waitlist (1 if so, 0 if not).';

ALTER TABLE  _bak_subscriber_archive ADD  membership_level VARCHAR( 20 ) NOT NULL DEFAULT '' 
COMMENT  'Subscriber''s membership level (Member, Pro, Exec, Early Bird, etc).';

-- Set value of membership_level field based on authorization amount
UPDATE _bak_subscriber_archive SET membership_level = if(amount = 60, 'Member', if(amount = 120, 'Pro', 
if(amount = 996, 'Exec', if(amount = 50 and creation_time < '2013-11-01 12:00:00' , 'Early Bird', 
if(amount = 50 and creation_time > '2013-11-01 12:00:00' , 'Late Bird', membership_level)))));

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
  token_validity_start_date timestamp NOT NULL COMMENT 'Date the token becomes valid.'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Deleted subscribers with authorizaton data.';

INSERT INTO subscriber_archive SELECT email, pwd, pwd_salt, creation_time, network_user_id, network_user_name, network,
full_name, follower_count, is_verified, oauth_access_token, oauth_access_token_secret, verification_code,
is_email_verified, is_from_waitlist, membership_level,
null,
null,
null,
null,
null,
null,
token_id, amount, status_code, error_message, payment_method_expiry,caller_reference, recurrence_period,
token_validity_start_date FROM _bak_subscriber_archive;


