<?php

class APIIPNController extends Controller {
    /**
     * Required IPN post values
     * @var array
     */
    var $REQUIRED_TRANSACTION_PARAMS = array('subscriptionId', 'paymentReason', 'transactionAmount', 'status',
        'buyerEmail', 'referenceId', 'buyerName', 'paymentMethod');

    public function control() {
        $debug = '';
        if (isset($_POST) && count($_POST) > 0) {
            $amazon_ipn_utils = new SignatureUtilsForOutbound();
            $ipn_endpoint = Config::getInstance()->getValue('amazon_ipn_endpoint');
            try {
                //IPN is sent as a http POST request and hence we specify POST as the http method.
                //Signature verification does not require your secret key
                if ($amazon_ipn_utils->validateRequest($_POST, $ipn_endpoint, "POST")) {
                    //Only log error states. Succeed quietly.
                    //$debug .= "Validated signature. ";
                    $subscription_operation_dao = new SubscriptionOperationMySQLDAO();
                    if ($this->isTransaction()) {
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

                            $subscription_operation_dao->insert($op);

                            //Set new paid_through date and update status
                            //Inefficient workaround alert:
                            //For inexplicable reasons, we have to retrieve the operation from the database here
                            //instead of just passing it to SubscriptionHelper
                            //because once it's inserted into the database, the transaction_date gets formatted
                            //correctly, in a way that PHP strtotime and date() just won't.
                            $op = $subscription_operation_dao->getByAmazonSubscriptionID($_POST['subscriptionId']);
                            $subscription_helper = new SubscriptionHelper();
                            $subscription_helper->updateSubscriptionStatusAndPaidThrough($subscriber, $op);

                            if ($op->status_code != 'PI') { //Don't notify about payment initiation
                                UpstartHelper::postToSlack('#signups',
                                    'Instant Pay Notification: '.$op->status_code." for ".$subscriber->thinkup_username
                                    .'\nhttps://www.thinkup.com/join/admin/subscriber.php?id='. $subscriber->id);
                            }

                            //If payment failed, cancel SimplePay subscription, user can come back and pay via Recurly
                            if ($op->status_code == 'PF') {
                                try {
                                    //Issue CancelAndRefund call to Amazon API
                                    $api_accessor = new AmazonFPSAPIAccessor();
                                    //Create a callerReference
                                    $caller_reference = $subscriber->id.'_'.time();
                                    $response = $api_accessor->cancelAndRefundSubscription($op->amazon_subscription_id,
                                        0, $caller_reference);
                                    UpstartHelper::postToSlack('#signups',
                                        "Cancelled SimplePay subscription. ".$subscriber->thinkup_username
                                        ." can return and re-pay via Recurly. "
                                        .'\nhttps://www.thinkup.com/join/admin/subscriber.php?id='. $subscriber->id);
                                } catch (Amazon_FPS_Exception $ex) {
                                    $debug = "Caught Exception: " . $ex->getMessage() . "\n";
                                    $debug .= "Response Status Code: " . $ex->getStatusCode() . "\n";
                                    $debug .= "Error Code: " . $ex->getErrorCode() . "\n";
                                    $debug .= "Error Type: " . $ex->getErrorType() . "\n";
                                    $debug .= "Request ID: " . $ex->getRequestId() . "\n";
                                    $debug .= "XML: " . $ex->getXML() . "\n";
                                    Logger::logError($debug, __FILE__, __LINE__, __METHOD__);
                                }
                            }
                        } else {
                            $debug .= "No past op found, subscriptionId ".$_POST['subscriptionId'];
                        }
                    } elseif ($this->isCancellation()) {
                        $sub_op = $subscription_operation_dao->getByAmazonSubscriptionID($_POST["subscriptionId"]);
                        if (isset($sub_op)) {
                            UpstartHelper::postToSlack('#signups',
                                $sub_op->buyer_name . ' subscription canceled due to '.$_POST['statusReason']
                                .'\nhttps://www.thinkup.com/join/admin/subscriber.php?id='. $sub_op->subscriber_id);
                        }
                    } else {
                        $debug = "Missing required parameters: ";
                        foreach ($this->REQUIRED_TRANSACTION_PARAMS as $req_param) {
                            if (!in_array($req_param, $_POST)) {
                                $debug .= $req_param . " ";
                            }
                        }
                    }
                } else {
                    $debug .= "Signature not validated!";
                }
            } catch (DuplicateSubscriptionOperationException $e) {
                $debug .= "DuplicateSubscriptionOperationException thrown";
            } catch (Exception $e ) {
                $debug .= get_class($e).": ".$e->getMessage();
            }
        }

        //If there's something to log, log it
        if ($debug !== '') {
            Logger::logError($debug, __FILE__,__LINE__,__METHOD__);
        }
    }
    /**
     * Did Amazon post all the required variables to parse a transaction?
     * @return bool
     */
    private function isTransaction() {
         // Check if all required POST vars for a transaction are set first
        $has_all_transaction_params = true;
        foreach ($this->REQUIRED_TRANSACTION_PARAMS as $param) {
            if (!isset($_POST[$param]) || $_POST[$param] == '' ) {
                $has_all_transaction_params = false;
            }
        }
        return $has_all_transaction_params;
   }
    /**
     * Did Amazon post data that indicates a subscription was cancelled?
     * @return bool
     */
    private function isCancellation() {
        return (isset($_POST["subscriptionId"]) && isset($_POST['status'])
            && $_POST['status'] === 'SubscriptionCancelled' && isset($_POST['statusReason']));
    }
}