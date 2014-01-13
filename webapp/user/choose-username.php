<?php
chdir('..');
require_once 'init.php';

$controller = new ChooseUsernameController();
echo $controller->control();
