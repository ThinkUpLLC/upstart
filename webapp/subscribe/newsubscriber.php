<?php
chdir('..');
require_once 'init.php';

$controller = new NewSubscriberController();
echo $controller->control();
