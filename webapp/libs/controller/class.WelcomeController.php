<?php
class WelcomeController extends SignUpController {
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
        $this->addToView('subscriber', $subscriber);

        if ($this->hasUserReturnedFromAmazon()) {
            $internal_caller_reference = SessionCache::get('caller_reference');
            if (isset($internal_caller_reference) && $this->isAmazonResponseValid($internal_caller_reference)) {
                $amazon_caller_reference = $_GET['callerReference'];
                $this->addToView('amazon_caller_reference', $amazon_caller_reference);

                $error_message = isset($_GET["errorMessage"])?$_GET["errorMessage"]:null;
                if ($error_message === null ) {
                    $this->addSuccessMessage("Thanks so much for subscribing to ThinkUp!");
                } else {
                    $this->addErrorMessage($this->generic_error_msg);
                    $this->logError("Amazon returned error: ".$error_message, __FILE__,__LINE__,__METHOD__);
                }

                //Record authorization
                $authorization_dao = new AuthorizationMySQLDAO();
                $amount = SignUpController::$subscription_levels[$_GET['level']];
                $payment_expiry_date = (isset($_GET['expiry']))?$_GET['expiry']:null;

                try {
                    $authorization_id = $authorization_dao->insert($_GET['tokenID'], $amount, $_GET["status"],
                        $internal_caller_reference, $error_message, $payment_expiry_date);

                    //Save authorization ID and subscriber ID in subscriber_authorizations table.
                    $subscriber_authorization_dao = new SubscriberAuthorizationMySQLDAO();
                    $subscriber_authorization_dao->insert($new_subscriber_id, $authorization_id);
                } catch (DuplicateAuthorizationException $e) {
                    $this->addSuccessMessage("Whoa there! It looks like you already paid for your ThinkUp ".
                    "subscription.  Did you refresh the page?");
                }

            } else {
                $this->addErrorMessage($this->generic_error_msg);
                if (!isset($internal_caller_reference)) {
                    $this->logError('Internal caller reference not set', __FILE__,__LINE__, __METHOD__);
                } else {
                    $this->logError('Amazon response invalid', __FILE__,__LINE__, __METHOD__);
                }
            }
        }
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
     * @param str $internal_caller_reference
     * @return bool
     */
    protected function isAmazonResponseValid($internal_caller_reference) {
        //Check inputs match internal rules
        $endpoint_url = UpstartHelper::getApplicationURL().'welcome.php?level='.$_GET['level'];
        $endpoint_url_params = array();
        return ($internal_caller_reference == $_GET['callerReference']
        && AmazonFPSAPIAccessor::isAmazonSignatureValid($endpoint_url, $endpoint_url_params));
    }
}
