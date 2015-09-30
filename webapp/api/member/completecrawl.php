<?php

chdir("..");
chdir("..");
require_once 'init.php';

$controller = new APICompleteCrawlController();
echo $controller->go();
