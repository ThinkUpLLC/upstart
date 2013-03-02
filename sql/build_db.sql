CREATE TABLE  user_routes (
id INT( 11 ) NOT NULL ,
email VARCHAR( 200 ) NOT NULL ,
pwd VARCHAR( 255 ) NOT NULL ,
pwd_salt VARCHAR( 255 ) NOT NULL ,
route VARCHAR( 100 ) NOT NULL ,
twitter_username VARCHAR( 255 ) NOT NULL ,
oauth_access_token VARCHAR( 255 ) NOT NULL ,
oauth_access_token_secret VARCHAR( 255 ) NOT NULL ,
is_verified TINYINT( 1 ) NOT NULL ,
follower_count INT( 11 ) NOT NULL
) COMMENT =  'Route users to their installation.';