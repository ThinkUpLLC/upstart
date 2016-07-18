<?php
chdir('..');
chdir('..');
require_once 'init.php';

$controller = new RefundAndCloseAccountsController();
$controller->control();