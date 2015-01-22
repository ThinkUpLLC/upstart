<?php
chdir('..');
chdir('..');
require_once 'init.php';

$controller = new UninstallExpiredAccountsController();
$controller->control();