<?php
chdir('..');
chdir('..');
require_once 'init.php';

$controller = new RemindAbandonsAboutPaymentController();
$controller->control();

$controller = new RemindTrialersAboutPaymentController();
$controller->control();