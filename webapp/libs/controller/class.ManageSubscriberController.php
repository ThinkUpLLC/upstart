<?php
/**
 * Show subscriber information and offer actions to modify/manage.
 * @author gina
 */
class ManageSubscriberController extends Controller {
    public function control() {
        $this->disableCaching();
        $this->setViewTemplate('admin-subscriber.tpl');

        $subscriber_id = (isset($_GET['id']))?(integer)$_GET['id']:false;
        if ($subscriber_id !== false ) {
            //Get subscriber and assign to view
            $subscriber_dao = new SubscriberMySQLDAO();
            $subscriber = $subscriber_dao->getByID($subscriber_id);
            $this->addToView('application_url', UpstartHelper::getApplicationURL());

            $subscriber_payment_dao = new SubscriberPaymentMySQLDAO();

            if (isset($subscriber)) {
                //Get authorizations and assign to view
                $subscriber_auth_dao = new SubscriberAuthorizationMySQLDAO();
                $authorizations = $subscriber_auth_dao->getBySubscriberID($subscriber_id);
                $this->addToView('authorizations', $authorizations);

                //If action specified, perform it
                if (isset($_GET['action'])) {
                    if ($_GET['action'] == 'archive') {
                        $result = $this->archiveSubscriber($subscriber_id);
                        if ($result) {
                            $this->addSuccessMessage("Subscriber archived.");
                            $subscriber = null;
                        } else {
                            $this->addErrorMessage("Subscriber does not exist.");
                        }
                    } elseif ($_GET['action'] == 'setusername') {
                        if (isset($_GET['username'])) {
                            $username = self::subdomainify($_GET['username']);
                            if ( $subscriber_dao->setUsername($subscriber_id, $username) ) {
                                $this->addSuccessMessage("Saved username $username.");
                                $subscriber = $subscriber_dao->getByID($subscriber_id);
                            }
                        } else {
                            $this->addErrorMessage("No username specified");
                        }
                    } elseif ($_GET['action'] == 'install') {
                        $installer = new AppInstaller();
                        try {
                            $install_results = $installer->install($subscriber_id, true);
                            $this->addSuccessMessage("Installation complete!");
                            $this->addToView('install_results', $install_results);
                            $subscriber = $subscriber_dao->getByID($subscriber_id);
                        } catch (Exception $e) {
                            $this->addErrorMessage("Could not install ThinkUp. ".$e->getMessage());
                        }
                    } elseif ($_GET['action'] == 'charge') {
                        if (isset($_GET['token_id']) && isset($_GET['amount'])) {
                            $ok = self::invokeAmazonPayAction($subscriber_id, $_GET['token_id'], $_GET['amount']);
                            if ($ok) {
                                $this->addSuccessMessage("Payment successful!");
                            } else {
                                $this->addErrorMessage("Payment failed!");
                            }
                        }  else {
                            $this->addErrorMessage("No token and/or amount specified");
                        }
                    }
                }

                //Set installation URL
                if (isset($subscriber->thinkup_username) && isset($subscriber->date_installed) ) {
                    $cfg = Config::getInstance();
                    $user_installation_url = $cfg->getValue('user_installation_url');
                    $subscriber->installation_url = str_replace ("{user}", $subscriber->thinkup_username,
                    $user_installation_url);
                }
                if (isset($subscriber) && !isset($subscriber->thinkup_username)) {
                    $subscriber->subdomainified_username = self::subdomainify($subscriber->network_user_name);
                }
                $this->addToView('subscriber', $subscriber);

                $payments = $subscriber_payment_dao->getBySubscriber($subscriber_id);
                $this->addToView('payments', $payments);
                $paid = false;
                foreach ($payments as $p) {
                    if (empty($p['error_message']) && strtotime($p['timestamp']) > (time() - (60*60*24*365))) {
                        $paid = true;
                        break;
                    }
                }
                $this->addToView('paid', $paid);

                $install_log_dao = new InstallLogMySQLDAO();
                $install_log_entries = $install_log_dao->getLogEntriesBySubscriber($subscriber_id);
                $this->addToView('install_log_entries', $install_log_entries);
            } else {
                $this->addErrorMessage("Subscriber does not exist.");
            }
        } else {
            $this->addErrorMessage("No subscriber specified.");
        }
        return $this->generateView();
    }

    /**
     * Archive subscriber and auth to subscribers_archived table, then delete auth, sub_auth, and subscriber.
     * @param int $subscriber_id
     * @return boolean Whether or not subscriber was archived
     */
    private function archiveSubscriber($subscriber_id) {
        $result = 0;
        $subscriber_dao = new SubscriberMySQLDAO();
        $result += $subscriber_dao->archiveSubscriber($subscriber_id);

        //Delete auth
        $auth_dao = new AuthorizationMySQLDAO();
        $result += $auth_dao->deleteBySubscriberID($subscriber_id);

        //Delete sub_auth
        $subscriber_auth_dao = new SubscriberAuthorizationMySQLDAO();
        $result += $subscriber_auth_dao->deleteBySubscriberID($subscriber_id);

        //Delete subscriber
        $result += $subscriber_dao->deleteBySubscriberID($subscriber_id);
        return ($result > 0);
    }

    /**
     * Convert username to valid characters for subdomains, ie, remove capital letters and special characters.
     * @param str $username
     * @return str $username
     */
    protected function subdomainify($username) {
        $username = strtolower($username);
        $username = preg_replace("/[^a-zA-Z0-9\s]/", "", $username);
        if ($username == '') {
            $unique = uniqid();
            $username .= substr($unique, strlen($unique)-4, strlen($unique));
        }
        return $username;
    }

    /**
     * Charge user, record transaction, and report success or error back.
     * @param str $token_id
     * @param int $amount
     * @return bool Did the payment succeed?
     */
    private function invokeAmazonPayAction($subscriber_id, $token_id, $amount) {
        $cfg = Config::getInstance();
        $AWS_ACCESS_KEY_ID = $cfg->getValue('AWS_ACCESS_KEY_ID');
        $AWS_SECRET_ACCESS_KEY = $cfg->getValue('AWS_SECRET_ACCESS_KEY');

        $service = new Amazon_FPS_Client($AWS_ACCESS_KEY_ID, $AWS_SECRET_ACCESS_KEY);

        $caller_reference = $subscriber_id.'_'.time();
        $payment_dao = new PaymentMySQLDAO();
        $subscriber_payment_dao = new SubscriberPaymentMySQLDAO();
        try {
            $params = array();
            $amount_params = array('Value'=>$amount, 'CurrencyCode'=>'USD');
            //REQUIRED PARAMS:
            $params['CallerReference'] = $caller_reference;
            $params['SenderTokenId'] = $token_id;
            $params['TransactionAmount'] = $amount_params;

            $request_object = new Amazon_FPS_Model_PayRequest($params);
            $response = $service->pay($request_object);


            $request_id = null;
            if ($response->isSetResponseMetadata()) {
                $responseMetadata = $response->getResponseMetadata();
                $request_id = $responseMetadata->getRequestId();
            }
            if ($response->isSetPayResult()) {
                $payResult = $response->getPayResult();
                $transaction_id = $payResult->getTransactionId();
                $status = $payResult->getTransactionStatus();
                $payment_id = $payment_dao->insert($transaction_id, $request_id, $status, $amount, $caller_reference);
                if ($payment_id) {
                    $subscriber_payment_dao->insert($subscriber_id, $payment_id);
                    return true;
                }
               $message = "Unable to store payment\n".$response->getXML();
               $payment_id = $payment_dao->insert(0, $request_id, '', 0, $caller_reference, $message);
               return false;
            }

            $message = "PayResult not returned\n".$response->getXML();
            $payment_id = $payment_dao->insert(0, $request_id, '', 0, $caller_reference, $message);
            return false;
        } catch (Amazon_FPS_Exception $ex) {
            $request_id = $ex->getRequestId();
            $message = $ex->getMessage() ."\n" . $ex->getXML();
            $payment_id = $payment_dao->insert(0, $request_id, '', 0, $caller_reference, $message);
            if ($payment_id) {
                $subscriber_payment_dao->insert($subscriber_id, $payment_id);
            }
        }
        return false;
    }

}
