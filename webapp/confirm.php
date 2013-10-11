<?php
require_once 'init.php';

$controller = new ConfirmEmailController();
echo $controller->control();
