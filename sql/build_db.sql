CREATE TABLE user_routes (
  id int(11) NOT NULL AUTO_INCREMENT,
  email varchar(200) NOT NULL,
  route varchar(100) NOT NULL,
  twitter_username varchar(255) NOT NULL,
  twitter_user_id varchar(30) NOT NULL,
  oauth_access_token varchar(255) NOT NULL,
  oauth_access_token_secret varchar(255) NOT NULL,
  is_verified tinyint(1) NOT NULL,
  follower_count int(11) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY email (email)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Route users to their installation.';
