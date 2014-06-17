<?php
chdir('..');
chdir('..');
require_once 'init.php';

$controller = new UpdatePendingPaymentStatusController();
$controller->control();