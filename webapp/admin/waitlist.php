<?php
chdir('..');
require_once 'init.php';

$controller = new ListUserController();
echo $controller->control();
