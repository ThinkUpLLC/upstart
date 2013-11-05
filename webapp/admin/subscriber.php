<?php
chdir('..');
require_once 'init.php';

$controller = new ManageSubscriberController();
echo $controller->control();
