<?php

class APIIPNController extends UpstartController {
    public function control() {
        $amazon_ipn_utils = new SignatureUtilsForOutbound();

        $ipn_endpoint = Config::getInstance()->getValue('amazon_ipn_endpoint');

        $debug = '';
        //@TODO Check if all required POST vars are set first
        if (isset($_POST['signature'])) {
            try {
                //IPN is sent as a http POST request and hence we specify POST as the http method.
                //Signature verification does not require your secret key
                if ($amazon_ipn_utils->validateRequest($_POST, $ipn_endpoint, "POST")) {
                    $debug .= "Signature correct. ";
                } else {
                    $debug .= "Signature not correct. ";
                }
            } catch (Exception $e ) {
                $debug = get_class($e).": ".$e->getMessage();
            }
            $subscription_operation_dao = new SubscriptionOperationMySQLDAO();
            $past_op = $subscription_operation_dao->getByAmazonSubscriptionID($_POST['subscriptionId']);
            if (isset($past_op)) {
                $subscriber_dao = new SubscriberMySQLDAO();
                $subscriber = $subscriber_dao->getByID($past_op->subscriber_id);

                $op = new SubscriptionOperation();
                $op->subscriber_id = $subscriber->id;
                $op->recurring_frequency = $past_op->recurring_frequency;
                $op->payment_reason = $_POST['paymentReason'];
                $op->transaction_amount = $_POST['transactionAmount'];
                $op->status_code = $_POST['status'];
                $op->buyer_email = $_POST['buyerEmail'];
                $op->reference_id = $_POST['referenceId'];
                $op->amazon_subscription_id = $_POST['subscriptionId'];
                $op->transaction_date = $_POST['transactionDate'];
                $op->buyer_name = $_POST['buyerName'];
                $op->operation = $_POST['operation'];
                $op->payment_method = $_POST['paymentMethod'];

                //Check to make sure this isn't a page refresh by catching a DuplicateKey exception
                try {
                    $subscription_operation_dao->insert($op);
                    //Now that user has authed and paid, get current subscription_status
                    $subscription_status = $subscriber->getSubscriptionStatus();
                    //Update subscription_status in the subscriber object
                    $subscriber->subscription_status = $subscription_status;
                    //Update subscription_status in the data store
                    $subscriber_dao->updateSubscriptionStatus($subscriber->id, $subscription_status);

                    //TODO Change this to a real channel once it's working
                    UpstartHelper::postToSlack('#testbot',
                        'Amazon Instant Pay Notification processed'
                        .'\nhttps://www.thinkup.com/join/admin/subscriber.php?id='. $subscriber->id);
                } catch (DuplicateSubscriptionOperationException $e) {
                    //Do nothing
                }
            } else {
                $debug .= "No past op found, subscriptionId ".$_POST['subscriptionId'];
            }
        } else {
            $debug = "Signature is not set.";
        }
        $this->logError($debug, __FILE__,__LINE__,__METHOD__);
    }
}