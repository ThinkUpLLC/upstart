ALTER TABLE  payments ADD INDEX (  transaction_status );

ALTER TABLE payments CHANGE error_message status_message VARCHAR(255)
NULL DEFAULT NULL COMMENT 'Human readable message that specifies the reason for a request failure (optional).';

ALTER TABLE payments COMMENT = 'Amazon FPS payment capture transactions';