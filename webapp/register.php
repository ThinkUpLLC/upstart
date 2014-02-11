<?php
require_once 'init.php';

$controller = new RegisterNewUserController();
echo $controller->go();