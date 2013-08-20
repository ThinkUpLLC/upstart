<?php

chdir('..');
require_once 'init.php';

$controller = new UpgradeApplicationController();
echo $controller->control();
