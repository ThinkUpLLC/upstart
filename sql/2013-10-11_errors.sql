CREATE TABLE error_log (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID.',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time error was thrown.',
  commit_hash varchar(50) NOT NULL COMMENT 'Git commit hash of code tree.',
  filename varchar(255) NOT NULL COMMENT 'Filename of the code where the error was thrown.',
  line_number int(11) NOT NULL COMMENT 'Line number in the code where the error was thrown.',
  method varchar(100) NOT NULL COMMENT 'Method where the error was thrown.',
  debug text NOT NULL COMMENT 'Debugging info.',
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Log of user errors.';