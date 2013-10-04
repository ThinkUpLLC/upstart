<?php
/*
 http://dev.upstart.com/subscribe/newaccount.php?l=member&
 tokenID=64QF9XGGXVD9UNI9S7KB53GYHLQEJRPSG8I8AM34MFGMCXVTD7HPKOEDNGDHS6D7&
 signatureMethod=RSA-SHA1&status=SC&signatureVersion=2&
 signature=HESfhdbwQfp0fYyx6%2BmpK%2F4N9tZPTe8N%2Fja0%2B32ab5suvlCqFJi4WU4JuteLY5O3zvuA4uNRJDwL%0A05FbSLhb3jZok4H9c
 %2BOJbF8VofF7qEUdwhXCHpoXD%2F5LaYTeGCNe8zuSpDupN1Fhcje9sr07oBrB%0AnXNkqKSQW8e7pS5rfvr2KA1eZWGWbnswRT09QJmcTSDzym5%
 2BGXilhkLIqDr0sxozM21dWprdqGWO%0ATkMoIj%2BHVfha2vE023%2B2p8PGAAb7yStWo4tlan7ER9Gj19qm1QR4fkF6oT5R1wkn1F2X%2Ft9uHzrl
 %0Ayn3L8iqFxebcfAtN3%2BzOQ6yr0a%2Fg4QJLKxWq6A%3D%3D&
 certificateUrl=https%3A%2F%2Ffps.amazonaws.com%2Fcerts%2F110713%2FPKICert.pem%3F
 requestId%3Dbk0guw95btzlsf08awrjgkgv2lw82id929cu6nwonow976zuek3
 &expiry=01%2F20
 */

chdir('..');
require_once 'init.php';

$controller = new NewSubscriberController();
echo $controller->control();
