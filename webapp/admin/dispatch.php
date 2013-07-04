<?php
chdir('..');
require_once 'init.php';

$controller = new DispatchCrawlJobsController();
echo $controller->control();
