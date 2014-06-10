ALTER TABLE subscribers ADD is_account_closed INT(1) NOT NULL DEFAULT '0' COMMENT 'Whether or not the member closed their account.';

ALTER TABLE subscriber_archive ADD is_account_closed INT(1) NOT NULL DEFAULT '0' COMMENT 'Whether or not the member closed their account.';