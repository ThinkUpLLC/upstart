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
                            self::invokeAmazonPayAction($_GET['token_id'], $_GET['amount']);
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
     * GT: Ripped this sample code directly from Amazon. Must rework to fit our app and record transactions.
     * @TODO: Create transaction model and DAO functions, remove all of the echo statements
     * @param str $token_id
     * @param int $amount
     */
    private function invokeAmazonPayAction($token_id, $amount) {
        $cfg = Config::getInstance();
        $AWS_ACCESS_KEY_ID = $cfg->getValue('AWS_ACCESS_KEY_ID');
        $AWS_SECRET_ACCESS_KEY = $cfg->getValue('AWS_SECRET_ACCESS_KEY');

        $service = new Amazon_FPS_Client($AWS_ACCESS_KEY_ID, $AWS_SECRET_ACCESS_KEY);

        $click_dao = new ClickMySQLDAO();
        $caller_reference = $click_dao->insert();
        try {
            $params = array();
            $amount_params = array('Value'=>$amount, 'CurrencyCode'=>'USD');
            //REQUIRED PARAMS:
            $params['CallerReference'] = $caller_reference;
            $params['SenderTokenId'] = $token_id;
            $params['TransactionAmount'] = $amount_params;

            $request_object = new Amazon_FPS_Model_PayRequest($params);
            echo "<pre>RESPONSE:
";
            //print_r($request_object);
            $response = $service->pay($request_object);


            print_r($response);
            echo ("Service Response\n");
            echo ("=============================================================================\n");

            echo("        PayResponse\n");
            if ($response->isSetPayResult()) {
                echo("            PayResult\n");
                $payResult = $response->getPayResult();
                if ($payResult->isSetTransactionId())
                {
                    echo("                TransactionId\n");
                    echo("                    " . $payResult->getTransactionId() . "\n");
                }
                if ($payResult->isSetTransactionStatus())
                {
                    echo("                TransactionStatus\n");
                    echo("                    " . $payResult->getTransactionStatus() . "\n");
                }
            }
            if ($response->isSetResponseMetadata()) {
                echo("            ResponseMetadata\n");
                $responseMetadata = $response->getResponseMetadata();
                if ($responseMetadata->isSetRequestId())
                {
                    echo("                RequestId\n");
                    echo("                    " . $responseMetadata->getRequestId() . "\n");
                }
            }

        } catch (Amazon_FPS_Exception $ex) {
            echo("Caught Exception: " . $ex->getMessage() . "\n");
            echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            echo("Error Code: " . $ex->getErrorCode() . "\n");
            echo("Error Type: " . $ex->getErrorType() . "\n");
            echo("Request ID: " . $ex->getRequestId() . "\n");
            echo("XML: " . $ex->getXML() . "\n");
        }
    }

}