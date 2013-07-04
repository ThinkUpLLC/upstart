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
  PRIMARY KEY (id),
  UNIQUE KEY email (email,twitter_user_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Route users to their installation.';