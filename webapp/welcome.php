<?php
require_once 'init.php';

$controller = new WelcomeController();
echo $controller->go();