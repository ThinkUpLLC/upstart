ALTER TABLE  subscribers CHANGE  is_from_waitlist  is_from_waitlist INT( 1 ) NOT NULL DEFAULT  '0' 
COMMENT  'Whether or not subscriber was on waitlist (1 if so, 0 if not).';

ALTER TABLE  subscribers CHANGE  is_installation_active  is_installation_active INT( 1 ) NULL DEFAULT NULL 
COMMENT  'Whether or not the installation is active.';
