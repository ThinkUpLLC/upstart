ALTER TABLE subscribers ADD is_crawl_in_progress INT(1) NOT NULL DEFAULT '0' COMMENT 'Whether or not there is a crawl in progress.';

ALTER TABLE subscribers ADD INDEX(is_crawl_in_progress);
