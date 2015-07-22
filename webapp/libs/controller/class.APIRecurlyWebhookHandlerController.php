<?php

class APIRecurlyWebhookHandlerController extends Controller {

    public function control() {
        //https://docs.recurly.com/client-libraries/php#handle_webhooks

        //Recurly will POST an XML payload to your URL that you designate
        //in your webhooks configuration

        //Get the XML Payload
        $post_xml = file_get_contents("php://input");
        $notification = new Recurly_PushNotification($post_xml);

        //Handle various notification types
        //closed_invoice_notification
        //new_account_notification
        //new_subscription_notification
        //billing_info_updated_notification
        //new_invoice_notification
        //canceled_account_notification
        //successful_refund_notification
        switch ($notification->type) {
            case "successful_payment_notification":
                $this->updateSubscriber($notification);
                break;
            case "failed_payment_notification":
                $this->updateSubscriber($notification);
                break;
          /* add more notifications to process */
            default:
//                 $debug = "Recurly webhook received that was not a payment notification
// ";
//                 $debug .= Utils::varDumpToString($notification);
//                 Logger::logError($debug, __FILE__,__LINE__,__METHOD__);
                break;
        }
    }


    private function updateSubscriber(Recurly_PushNotification $notification) {
        try {
            if (isset($notification->transaction->subscription_id)) {
                // Required for the Recurly API
                $cfg = Config::getInstance();
                Recurly_Client::$subdomain = $cfg->getValue('recurly_subdomain');
                Recurly_Client::$apiKey = $cfg->getValue('recurly_api_key');

                $subscription = Recurly_Subscription::get($notification->transaction->subscription_id);
                $account = $subscription->account->get();

                //Get subscriber based on account email
                $subscriber_dao = new SubscriberMySQLDAO();
                try {
                    $subscriber = $subscriber_dao->getByEmail($account->email);
                } catch (SubscriberDoesNotExistException $e) {
                    //If this is a new Recurly subscription that was imported from Amazon, the email address
                    //may not match the ThinkUp registration address.
                    //Check subscription operations table
                    $sub_op_dao = new SubscriptionOperationMySQLDAO();
                    $operation = $sub_op_dao->getLatestOperationByBuyerEmail($account->email);
                    $subscriber = $subscriber_dao->getByID($operation->subscriber_id);
                }

                //Get recurrence based on subscription plan
                if (strpos($subscription->plan->plan_code, 'monthly') !== false) {
                    $subscriber->subscription_recurrence = '1 month';
                } elseif (strpos($subscription->plan->plan_code, 'yearly') !== false) {
                    $subscriber->subscription_recurrence = '12 months';
                }

                if ($notification->type == 'successful_payment_notification') {
                    $subscriber->subscription_status = 'Paid';
                    //Get paid through date based on subscription
                    $subscriber->paid_through =
                        $subscription->current_period_ends_at->format('Y-m-d H:i:s');
                } elseif ($notification->type == 'failed_payment_notification'){
                    $subscriber->subscription_status = 'Payment failed';
                }

                //Get subscription_id
                $subscriber->recurly_subscription_id = $subscription->uuid;
                $subscriber->is_via_recurly = true;

                //Update data store
                $subscriber_dao->setSubscriptionDetails($subscriber);

                UpstartHelper::postToSlack('#webhooks',
                    'Recurly webhook received: '.$notification->type." for ".$notification->account->email
                    .'\nhttps://thinkup.recurly.com/subscriptions/'.
                    urlencode($notification->transaction->subscription_id).'\n'
                    .'https://thinkup.com/join/admin/subscriber.php?id='.$subscriber->id);

                $debug = "Received Recurly Webhook, got subscription
";
                $debug .= Utils::varDumpToString($subscription);
                $debug .= Utils::varDumpToString($account);
            } else {
                $debug = "No notification->transaction->subscription_id found in notification
";
                $debug .= Utils::varDumpToString($notification);
            }
            Logger::logError($debug, __FILE__,__LINE__,__METHOD__);
        } catch (Recurly_NotFoundError $e) {
            $debug = get_class($e) . ': Record could not be found';
            Logger::logError($debug, __FILE__,__LINE__,__METHOD__);
        } catch (Recurly_ValidationError $e) {
            // If there are multiple errors, they are comma delimited:
            $messages = explode(',', $e->getMessage());
            $debug = get_class($e) . ': Validation problems: ' . implode("\n", $messages);
            Logger::logError($debug, __FILE__,__LINE__,__METHOD__);
        } catch (Recurly_ServerError $e) {
            $debug = get_class($e) . ': Problem communicating with Recurly';
            Logger::logError($debug, __FILE__,__LINE__,__METHOD__);
        } catch (Exception $e) {
            $debug = get_class($e) . ': ' . $e->getMessage();
            Logger::logError($debug, __FILE__,__LINE__,__METHOD__);
        }
    }
}
