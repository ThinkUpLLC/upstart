<?php
chdir('..');
require_once 'init.php';

$controller = new ListErrorController();
echo $controller->control();
