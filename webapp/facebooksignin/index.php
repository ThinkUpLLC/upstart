<?php
chdir('..');
require_once 'init.php';

$controller = new FacebookSignInController();
echo $controller->go();