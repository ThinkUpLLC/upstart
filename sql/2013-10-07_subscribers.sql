ALTER TABLE  subscribers ADD  pwd VARCHAR( 255 ) NOT NULL COMMENT  'Subscriber password.' AFTER  email ,
ADD  pwd_salt VARCHAR( 255 ) NOT NULL COMMENT  'Subscriber password salt.' AFTER  pwd;

ALTER TABLE  subscriber_authorizations ADD UNIQUE (
subscriber_id ,
authorization_id
)