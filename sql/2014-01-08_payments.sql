CREATE TABLE payments (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT  'Internal unique ID.',
  timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time of transaction.',
  transaction_id varchar(100) NOT NULL COMMENT 'Transaction ID of payment from Amazon.',
  request_id varchar(100) NOT NULL COMMENT 'Request ID of transaction, assigned by Amazon.',
  transaction_status varchar(20) NOT NULL COMMENT 'The status of the payment request.',
  error_message varchar(255) DEFAULT NULL COMMENT 'Human readable message that specifies the reason for a request failure (optional).'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Amazon FPS payment capture transacrions';

CREATE TABLE  subscriber_payments (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT  'Internal unique ID.',
  timestamp TIMESTAMP NOT NULL COMMENT  'Time transaction was recorded.',
  subscriber_id INT NOT NULL COMMENT  'Subscriber ID keyed to subscribers.',
  payment_id INT NOT NULL COMMENT  'Payment ID keyed to payments.',
  INDEX (subscriber_id)
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT =  'Payments by known subscribers.';
