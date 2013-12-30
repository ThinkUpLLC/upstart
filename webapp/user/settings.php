<?php
chdir('..');
require_once 'init.php';

$controller = new SettingsController();
echo $controller->control();
