<?php
chdir('..');
chdir('..');
require_once 'init.php';

$controller = new UninstallOverduePaymentsController();
$controller->control();