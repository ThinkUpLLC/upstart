<?php
require_once 'init.php';

$controller = new PayNowController();
echo $controller->go();