<?php
chdir('..');
require_once 'init.php';

$controller = new CheckUsernameController();
echo $controller->control();
