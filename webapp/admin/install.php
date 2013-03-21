<?php
chdir('..');
require_once 'init.php';

$controller = new InstallApplicationController();
echo $controller->control();
