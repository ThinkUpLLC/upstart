<?php
chdir('..');
chdir('..');
require_once 'init.php';

$controller = new ListClaimCodesController();
echo $controller->control();