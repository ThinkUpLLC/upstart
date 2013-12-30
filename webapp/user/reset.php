<?php
chdir('..');
require_once 'init.php';

$controller = new ResetPasswordController();
echo $controller->control();
