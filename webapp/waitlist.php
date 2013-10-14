<?php
require_once 'init.php';

$controller = new RouteUserController();
echo $controller->go();
