ALTER TABLE  subscribers CHANGE  network_user_id  network_user_id VARCHAR( 30 ) 
CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT  'Subscriber''s network user ID.';

ALTER TABLE  subscribers CHANGE  network  network VARCHAR( 20 )
CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT  'Subscriber''s authorized network, ie, Twitter or Facebook.';

ALTER TABLE  subscribers ADD UNIQUE (
network_user_id,
network
);
