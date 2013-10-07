<?php
chdir('..');
require_once 'init.php';

$controller = new SubscribeController();
echo $controller->control();
