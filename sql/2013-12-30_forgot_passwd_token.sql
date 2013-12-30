ALTER TABLE  subscribers  
ADD password_token varchar(64) DEFAULT NULL COMMENT 'Password reset token.';