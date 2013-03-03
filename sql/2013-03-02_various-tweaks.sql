ALTER TABLE  user_routes ADD  twitter_user_id VARCHAR( 30 ) NOT NULL AFTER  twitter_username;

ALTER TABLE  user_routes ADD PRIMARY KEY (  id )
ALTER TABLE  user_routes CHANGE  id  id INT( 11 ) NOT NULL AUTO_INCREMENT;

ALTER TABLE  user_routes DROP pwd;
ALTER TABLE  user_routes DROP pwd_salt;

ALTER TABLE  user_routes ADD UNIQUE ( email );
