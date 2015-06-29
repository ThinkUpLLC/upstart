<?php
chdir('..');
chdir('..');
require_once 'init.php';

$controller = new RemindTrialersAboutPaymentController();
$controller->control();

$controller = new RemindAnnualsAboutReupController();
$controller->control();