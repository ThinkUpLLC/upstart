ALTER TABLE  subscribers 
ADD timezone VARCHAR( 50 ) NOT NULL DEFAULT  'UTC' COMMENT  'Subscriber timezone.';
