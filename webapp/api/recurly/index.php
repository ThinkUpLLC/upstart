<?php
chdir('..');
chdir('..');
require_once 'init.php';

$controller = new APIRecurlyWebhookHandlerController();
echo $controller->control();
