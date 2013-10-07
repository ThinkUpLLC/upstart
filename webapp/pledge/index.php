<?php
chdir('..');
require_once 'init.php';

$controller = new PledgeController();
echo $controller->control();
