<?php
chdir('..');
chdir('..');
require_once 'init.php';

$controller = new RemindTrialersAboutPaymentController();
$controller->control();

//Temporarily disabled until it's converted to FPS transition marketing messages
// $controller = new RemindAnnualsAboutReupController();
// $controller->control();