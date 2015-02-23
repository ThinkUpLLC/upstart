ALTER TABLE subscribers ADD is_via_recurly INT(1) NOT NULL DEFAULT '0' COMMENT 'Whether or not subscription created via Recurly.' ;
