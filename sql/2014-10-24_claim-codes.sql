CREATE TABLE claim_codes (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal, unique ID.',
  `code` varchar(24) NOT NULL COMMENT 'Unique, user-facing claim code.',
  `type` varchar(50) NOT NULL COMMENT 'Purchase association - bundle, gift, etc.',
  operation_id int(11) NOT NULL COMMENT 'Operation ID of code purchase.',
  is_redeemed int(1) NOT NULL DEFAULT 0 COMMENT 'Whether or not the code is redeemed.',
  redemption_date timestamp NULL COMMENT 'When the code was redeemed.',
  number_days int(11) NOT NULL COMMENT 'How many days of membership this code represents.',
  PRIMARY KEY (id),
  UNIQUE KEY `code` (`code`),
  KEY operation_id (operation_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Claim codes for membership units of time.';


-- --------------------------------------------------------

--
-- Table structure for table 'claim_code_operations'
--

CREATE TABLE claim_code_operations (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID.',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of insertion.',
  transaction_id varchar(100) NOT NULL COMMENT 'Amazon transaction ID.',
  reference_id varchar(20) NOT NULL COMMENT 'Caller reference for transaction.',
  buyer_email varchar(255) NOT NULL COMMENT 'Amazon''s buyer email address.',
  buyer_name varchar(255) NOT NULL COMMENT 'Amazon''s buyer name.',
  transaction_amount varchar(100) NOT NULL COMMENT 'Amount of transaction.',
  status_code varchar(50) NOT NULL COMMENT 'Transaction status code.',
  `type` varchar(50) NOT NULL COMMENT 'Purchase association - bundle, gift, etc.',
  number_days int(11) NOT NULL COMMENT 'How many days of membership this code represents.',
  PRIMARY KEY (id),
  UNIQUE KEY amazon_reference_id (reference_id,status_code)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Amazon claim code purchase operations.';
