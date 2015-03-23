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
        switch ($notification->type) {
            case "successful_payment_notification":
                $this->updateSubscriber($notification);
                break;
            case "failed_payment_notification":
                $this->updateSubscriber($notification);
                break;
            default:
                $debug = "Recurly webhook received that was not a payment notification
";
                $debug .= Utils::varDumpToString($notification);
                Logger::logError($debug, __FILE__,__LINE__,__METHOD__);
                break;
          /* add more notifications to process */
        }
    }


    private function updateSubscriber(Recurly_PushNotification $notification) {
        try {
            if (isset($notification->transaction->subscription_id)) {
                $subscription = Recurly_Subscription::get($notification->transaction->subscription_id);

                UpstartHelper::postToSlack('#signups',
                    'Recurly webhook received: '.$notification->type." for ".$notification->account->email
                    .'\nhttps://thinkup.recurly.com/subscriptions/'.
                    urlencode($notification->transaction->subscription_id));

                $debug = "Received Recurly Webhook, got subscription
";
                $debug .= Utils::varDumpToString($subscription);
            } else {
                $debug = "No notification->transaction->subscription_id found in notitification
";
                $debug .= Utils::varDumpToString($notification);
            }
            Logger::logError($debug, __FILE__,__LINE__,__METHOD__);

            //print $subscription->activated_at->format(DateTime::ISO8601);


            // $subscriber_dao = new SubscriberMySQLDAO();
            // if ($notification->type == "failed_payment_notification") {
            //     $subscription_status = 'Payment failed';
            //     $result = $subscriber_dao->setSubscriptionStatus($subscriber->id, $subscription_status);

            // } elseif ($notification->type == "successful_payment_notification") {
            //     $subscription_status = 'Paid';
            //     $result = $subscriber_dao->setSubscriptionStatus($subscriber->id, $subscription_status);

            //     $paid_through_time = strtotime('+'.$operation->recurring_frequency,
            //         strtotime($operation->transaction_date));
            //     $paid_through_time = date('Y-m-d H:i:s', $paid_through_time);
            //     $result += $subscriber_dao->setPaidThrough($subscriber->id, $paid_through_time);
            //     $result += $subscriber_dao->setSubscriptionRecurrence($subscriber->id, $operation->recurring_frequency);
            // }
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
