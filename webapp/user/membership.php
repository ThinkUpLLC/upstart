<?php
chdir('..');
require_once 'init.php';

$controller = new MembershipController();
echo $controller->control();
