<?php
chdir('..');
chdir('..');
require_once 'init.php';

$controller = new PaymentReminderController();
$controller->control();