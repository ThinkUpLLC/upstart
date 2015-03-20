<?php

class APIRecurlyWebhookHandlerController extends Controller {

    public function control() {
        //@TODO Smartly handle post vars, just capturing and logging for now for testing purposes
        $debug = "Received Recurly Webhook";
        Logger::logError($debug, __FILE__,__LINE__,__METHOD__);
    }
}
