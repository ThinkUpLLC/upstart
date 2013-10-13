CREATE TABLE  subscriber_counts (
amount INT NOT NULL COMMENT  'Amount of yearly subscription.',
count INT NOT NULL COMMENT  'Total subscribers at this amount.',
PRIMARY KEY (  amount )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT =  'Cached totals of backer counts at subscription levels.';

INSERT INTO subscriber_counts (amount, count) SELECT amount, count(id) as count FROM authorizations;