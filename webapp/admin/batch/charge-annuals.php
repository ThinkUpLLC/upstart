<?php
chdir('..');
chdir('..');
require_once 'init.php';

$controller = new ChargeAnnualSubscribersController();
$controller->control();