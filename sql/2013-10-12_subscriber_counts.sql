CREATE TABLE  subscriber_counts (
amount INT NOT NULL COMMENT  'Amount of yearly subscription.',
count INT NOT NULL COMMENT  'Total subscribers at this amount.',
PRIMARY KEY (  amount )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT =  'Cached totals of backer counts at subscription levels.';

--
-- Reusable SQL to refresh cached subscriber counts and remove orphan authorizations from total backer counts
--
TRUNCATE TABLE subscriber_counts;

INSERT INTO subscriber_counts (amount, count) 
SELECT a.amount, count(a.id) as count FROM authorizations a 
INNER JOIN subscriber_authorizations sa ON sa.authorization_id = a.id GROUP BY a.amount;