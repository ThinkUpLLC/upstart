<?php
chdir('..');
require_once 'init.php';

$controller = new BundleController();
echo $controller->go();