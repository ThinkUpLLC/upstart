ALTER TABLE subscribers
ADD last_crawl_completed  timestamp NULL DEFAULT NULL COMMENT 'Time the last crawl was completed.';