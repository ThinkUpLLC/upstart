ALTER TABLE  subscribers ADD  pwd VARCHAR( 255 ) NOT NULL COMMENT  'Subscriber password.' AFTER  email ,
ADD  pwd_salt VARCHAR( 255 ) NOT NULL COMMENT  'Subscriber password salt.' AFTER  pwd;

ALTER TABLE  subscriber_authorizations ADD UNIQUE (
subscriber_id ,
authorization_id
)

ALTER TABLE  subscribers ADD  follower_count INT( 11 ) NOT NULL COMMENT  'Follower or subscriber count of service user.' AFTER  full_name ,
ADD  is_verified INT( 1 ) NOT NULL COMMENT  'Whether or not the service user is verified.' AFTER  follower_count;