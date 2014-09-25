<?php

class APIIPNController extends UpstartController {
    /**
     * Required IPN post values
     * @var array
     */
    var $REQUIRED_PARAMS = array('signature', 'subscriptionId', 'paymentReason', 'transactionAmount', 'status',
        'buyerEmail', 'referenceId', 'subscriptionId', 'buyerName', 'paymentMethod');
    /**
     *
     * @var bool
     */
    var $is_missing_param = false;

    public function control() {
        $amazon_ipn_utils = new SignatureUtilsForOutbound();

        $ipn_endpoint = Config::getInstance()->getValue('amazon_ipn_endpoint');

        $debug = '';
        //@TODO Check if all required POST vars are set first
        foreach ($this->REQUIRED_PARAMS as $param) {
            if (!isset($_POST[$param]) || $_POST[$param] == '' ) {
                $this->is_missing_param = true;
            }
        }

        if (isset($_POST) && count($_POST) > 0) {
            if (!$this->is_missing_param) {
                try {
                    //IPN is sent as a http POST request and hence we specify POST as the http method.
                    //Signature verification does not require your secret key
                    if ($amazon_ipn_utils->validateRequest($_POST, $ipn_endpoint, "POST")) {
                        $debug .= "Validated signature. ";
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
                            $op->buyer_name = $_POST['buyerName'];
                            $op->payment_method = $_POST['paymentMethod'];
                            $op->operation = (isset($_POST['operation']))?$_POST['operation']:'unspecified';
                            $op->transaction_date = (isset($_POST['transactionDate']))?
                                $_POST['transactionDate']:"NOW()";

                            //Check to make sure this isn't a page refresh by catching a DuplicateKey exception
                            try {
                                $subscription_operation_dao->insert($op);
                                //Now that user has authed and paid, get current subscription_status
                                $subscription_status = $subscriber->getSubscriptionStatus();
                                //Update subscription_status in the subscriber object
                                $subscriber->subscription_status = $subscription_status;
                                //Update subscription_status in the data store
                                $subscriber_dao->updateSubscriptionStatus($subscriber->id, $subscription_status);

                                UpstartHelper::postToSlack('#signups',
                                    'Instant Pay Notification: '.$op->status_code." for ".$subscriber->thinkup_username
                                    .'\nhttps://www.thinkup.com/join/admin/subscriber.php?id='. $subscriber->id);
                            } catch (DuplicateSubscriptionOperationException $e) {
                                $debug .= "DuplicateSubscriptionOperationException thrown";
                            }
                        } else {
                            $debug .= "No past op found, subscriptionId ".$_POST['subscriptionId'];
                        }
                    } else {
                        $debug .= "Signature not validated!";
                    }
                } catch (Exception $e ) {
                    $debug .= get_class($e).": ".$e->getMessage();
                }
            } else {
                $debug = "Missing required parameters: ";
                foreach ($this->REQUIRED_PARAMS as $req_param) {
                    if (!in_array($req_param, $_POST)) {
                        $debug .= $req_param . " ";
                    }
                }
            }
        }

        //If there's something to log, log it
        if ($debug !== '') {
            $this->logError($debug, __FILE__,__LINE__,__METHOD__);
        }
    }
}