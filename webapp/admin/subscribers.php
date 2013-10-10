<?php
chdir('..');
require_once 'init.php';

$controller = new ListSubscriberController();
echo $controller->control();
