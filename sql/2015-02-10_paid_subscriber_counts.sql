-- --------------------------------------------------------

--
-- Table structure for table 'subscriber_paid_counts'
--

CREATE TABLE subscriber_paid_counts (
  id int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  count int(11) NOT NULL,
  PRIMARY KEY (id),
  KEY `date` (`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Paid subscriber totals by date.';