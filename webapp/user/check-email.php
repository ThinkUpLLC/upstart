<?php
chdir('..');
require_once 'init.php';

$controller = new CheckEmailController();
echo $controller->control();
