<?php
require_once 'init.php';

$controller = new ConfirmPaymentController();
echo $controller->control();
