<?php
chdir('..');
require_once 'init.php';

$controller = new ConfirmSubscriberController();
echo $controller->control();
