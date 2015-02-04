<?php

chdir("..");
chdir("..");
require_once 'init.php';

$controller = new APIMemberStatusController();
echo $controller->go();
