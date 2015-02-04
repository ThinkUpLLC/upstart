<?php
chdir('..');
chdir('..');
require_once 'init.php';

$controller = new RemindTrialersAboutPaymentController();
$controller->control();

//@TODO Disable this once Amazon FPS stops working (June 1)
$controller = new RemindAnnualsAboutReupController();
$controller->control();