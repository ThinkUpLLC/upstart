<?php
chdir('..');
require_once 'init.php';

$controller = new ListStatsController();
echo $controller->control();
