ALTER TABLE  user_routes CHANGE  email  email VARCHAR( 70 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE  user_routes DROP INDEX  email , ADD UNIQUE  email (  email ,  twitter_user_id );