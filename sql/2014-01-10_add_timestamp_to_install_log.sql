ALTER TABLE install_log ADD timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time of log entry.' ,
ADD INDEX (timestamp) ;