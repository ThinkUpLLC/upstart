<?php
chdir('..');
require_once 'init.php';

$controller = new TwitterSignInController();
echo $controller->go();