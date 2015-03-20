<?php

class APIRecurlyWebhookHandlerController extends Controller {

    public function control() {
        //https://docs.recurly.com/client-libraries/php#handle_webhooks

        //Recurly will POST an XML payload to your URL that you designate
        //in your webhooks configuration

        //Get the XML Payload
        $post_xml = file_get_contents ("php://input");
        $notification = new Recurly_PushNotification($post_xml);
        //@TODO Smartly handle various notification types, just capturing and logging for now for testing purposes
        //each webhook is defined by a type
        // switch ($notification->type) {
        //   case "successful_payment_notification":
        //     /* process notification here */
        //     break;
        //   case "failed_payment_notification":
        //     /* process notification here */
        //     break;
        //   /* add more notifications to process */
        // }

        $debug = "Received Recurly Webhook
";
        $debug .= Utils::varDumpToString($notification);
        Logger::logError($debug, __FILE__,__LINE__,__METHOD__);
    }
}
