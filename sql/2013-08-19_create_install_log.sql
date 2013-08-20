ALTER TABLE  user_routes ADD  commit_hash VARCHAR( 41 ) NOT NULL COMMENT  'Git commit hash of installation version.';

CREATE TABLE  install_log (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT  'Internal unique ID.',
user_route_id INT NOT NULL COMMENT  'User route ID.',
commit_hash VARCHAR( 41 ) NOT NULL COMMENT  'Git commit hash of installation version.',
migration_success TINYINT NOT NULL COMMENT  'Whether or not install/upgrade was successful.',
migration_message TEXT NOT NULL COMMENT  'Install/upgrade debug message.'
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT =  'Installation upgrade log.';