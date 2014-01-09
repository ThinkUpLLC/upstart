ALTER TABLE payments ADD amount int COMMENT 'Amount of payment in USD.';
ALTER TABLE payments ADD caller_reference varchar(24) COMMENT 'Caller reference used for charge request.';
