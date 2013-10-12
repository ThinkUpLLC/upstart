<?php
chdir('..');
require_once 'init.php';

$controller = new ChargeController();
echo $controller->control();
