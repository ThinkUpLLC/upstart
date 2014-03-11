<?php
class WelcomeController extends SignUpHelperController {
    /**
     * @var array Options for notification frequency
     */
    protected $notification_frequencies = array('daily'=>'Daily','weekly'=>'Weekly', 'never'=>'Never');

    public function control() {
        $this->disableCaching();
        $this->setPageTitle('Welcome');
        $this->setViewTemplate('welcome.tpl');

        $new_subscriber_id = SessionCache::get('new_subscriber_id');
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByID($new_subscriber_id);

        if ($this->hasUserReturnedFromAmazon()) {
            if ($this->isAmazonResponseValid()) {
                $amazon_caller_reference = $_GET['callerReference'];

                $error_message = isset($_GET["errorMessage"])?$_GET["errorMessage"]:null;
                if ($error_message !== null ) {
                    $this->addErrorMessage($this->generic_error_msg);
                    $this->logError("Amazon returned error: ".$error_message, __FILE__,__LINE__,__METHOD__);
                }

                //Record authorization
                $authorization_dao = new AuthorizationMySQLDAO();
                $amount = SignUpHelperController::$subscription_levels[$_GET['level']];
                $payment_expiry_date = (isset($_GET['expiry']))?$_GET['expiry']:null;

                try {
                    $authorization_id = $authorization_dao->insert($_GET['tokenID'], $amount, $_GET["status"],
                        $amazon_caller_reference, $error_message, $payment_expiry_date);

                    //Save authorization ID and subscriber ID in subscriber_authorizations table.
                    $subscriber_authorization_dao = new SubscriberAuthorizationMySQLDAO();
                    $subscriber_authorization_dao->insert($new_subscriber_id, $authorization_id);
                } catch (DuplicateAuthorizationException $e) {
                    $this->addSuccessMessage("Whoa there! It looks like you already paid for your ThinkUp ".
                    "subscription.  Did you refresh the page?");
                }
            } else {
                $this->addErrorMessage($this->generic_error_msg);
                $this->logError('Amazon response invalid', __FILE__,__LINE__, __METHOD__);
            }
        }
        $cfg = Config::getInstance();
        $user_installation_url = $cfg->getValue('user_installation_url');
        $subscriber->installation_url = str_replace ("{user}", $subscriber->thinkup_username,
            $user_installation_url);
        $this->addToView('new_subscriber', $subscriber);

        return $this->generateView();
    }

    /**
     * Return whether or not user has returned from Amazon with necessary parameters.
     * @return bool
     */
    protected function hasUserReturnedFromAmazon() {
        return (isset($_GET['callerReference'])  && isset($_GET['tokenID'])  && isset($_GET['level'])
            && isset($_GET['status']) && isset($_GET['certificateUrl']) && isset($_GET['signatureMethod'])
            && isset($_GET['signature']));
    }

    /**
     * Return whether or not Amazon signature is valid.
     * @return bool
     */
    protected function isAmazonResponseValid() {
        //Check inputs match internal rules
        $endpoint_url = UpstartHelper::getApplicationURL().'welcome.php?level='.$_GET['level'];
        $endpoint_url_params = array();
        return AmazonFPSAPIAccessor::isAmazonSignatureValid($endpoint_url, $endpoint_url_params);
    }
}
