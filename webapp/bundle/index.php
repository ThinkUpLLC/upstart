<?php
chdir('..');
require_once 'init.php';

$controller = new BundleLandingController();
echo $controller->go();