ALTER TABLE  subscribers 
ADD is_activated int(1) NOT NULL DEFAULT '0' COMMENT 'If user is activated, 1 for true, 0 for false.',
ADD last_login date NULL DEFAULT NULL COMMENT 'Last time member logged in.',
ADD failed_logins int(11) NOT NULL DEFAULT '0' COMMENT 'Current number of failed login attempts.',
ADD account_status varchar(150) NOT NULL DEFAULT '' COMMENT 'Description of account status, i.e., "Inactive due to excessive failed login attempts".';