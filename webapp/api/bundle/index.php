<?php

chdir("..");
chdir("..");
require_once 'init.php';

$controller = new APIValidClaimCodeController();
echo $controller->go();
