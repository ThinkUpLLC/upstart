<?php
chdir('..');
chdir('..');
require_once 'init.php';

$controller = new APIIPNController();
echo $controller->control();
