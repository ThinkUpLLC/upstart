<?php
class BundleController extends SignUpHelperController {
    /**
     * Name of the bundle
     * @var string
     */
    var $name = "The Better Web Bundle 2014";
    /**
     * The total number of days of membership the user is purchasing.
     * @var integer
     */
    var $total_days = 365;
    /**
     * Price of the bundle.
     * @var integer
     */
    var $price = 100;

    public function control() {
        $this->setViewTemplate('bundle.index.tpl');
        $this->disableCaching();
        $this->setPageTitle($this->name);
        $this->addToView('title', $this->name);

        if ($this->hasUserReturnedFromAmazon()) {
            if ($this->isAmazonResponseValid()) {
                $claim_code_op_dao = new ClaimCodeOperationMySQLDAO();
                try {
                    $operation_id = $claim_code_op_dao->insert( $_GET['referenceId'],  $_GET['transactionId'],
                        $_GET['buyerEmail'], $_GET['buyerName'], $_GET['transactionAmount'], $_GET['status'],
                        $this->name, $this->total_days);
                    if (isset($operation_id) && $_GET['status'] == 'PS') {
                        $claim_code_dao = new ClaimCodeMySQLDAO();
                        $claim_code = $claim_code_dao->insert( $this->name, $operation_id, $this->total_days);
                        $this->addSuccessMessage('Success! You\'ve purchased '. $this->name);
                        $this->addToView('claim_code', $claim_code);
                        $claim_code_readable = $claim_code_dao->makeClaimCodeReadable($claim_code);
                        $this->addToView('claim_code_readable', $claim_code_readable);
                        $this->addToView('reference_id', $_GET['referenceId']);
                        $this->addToView('transaction_id', $_GET['transactionId']);
                        // TODO Validate email address
                        // Send email to Amazon email address
                        self::sendConfirmationEmail($_GET['buyerEmail'], $claim_code, $claim_code_readable);
                    } else {
                        $this->addErrorMessage($this->generic_error_msg);
                        $this->logError('No claim code operation inserted or status is not PS '.$_GET['status'],
                            __FILE__,__LINE__, __METHOD__);
                    }
                } catch (DuplicateClaimCodeOperationException $e) {
                    $this->addErrorMessage("Looks like you already bought the bundle. Did you reload the page?");
                }
            } else {
                $this->addErrorMessage($this->generic_error_msg);
                $this->logError('Amazon response invalid', __FILE__,__LINE__, __METHOD__);
            }
        } else {
            //Get Amazon URL
            $caller_reference = md5(uniqid(rand())).time();
            $callback_url = UpstartHelper::getApplicationURL();
            $api_accessor = new AmazonFPSAPIAccessor();
            $pay_with_amazon_form = $api_accessor->generateStandardForm('USD '.$this->price, $this->name,
                $caller_reference, $callback_url);

            $this->addToView('pay_with_amazon_form', $pay_with_amazon_form);
        }

        return $this->generateView();
    }

    /**
     * Return whether or not user has returned from Amazon with necessary parameters.
     * @return bool
     */
    protected function hasUserReturnedFromAmazon() {
        return (UpstartHelper::areGetParamsSet(SignUpHelperController::$amazon_simple_pay_standard_return_params));
    }

    /**
     * Return whether or not Amazon signature is valid.
     * @return bool
     */
    protected function isAmazonResponseValid() {
        //Check inputs match internal rules
        $endpoint_url = UpstartHelper::getApplicationURL();
        $endpoint_url_params = array();
        return AmazonFPSAPIAccessor::isAmazonSignatureValid($endpoint_url, $endpoint_url_params);
    }

    protected function sendConfirmationEmail($email, $code, $code_readable) {
        $template_name = "Upstart System Messages";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $subject = $this->name;

        $cfg = Config::getInstance();
        $api_key = $cfg->getValue('mandrill_api_key');

        $email_view_mgr->assign('site_url', UpstartHelper::getApplicationURL(false, false, false) );
        $email_view_mgr->assign('claim_code', $code );
        $email_view_mgr->assign('claim_code_readable', $code_readable );

        $email_view_mgr->assign('name', $this->name );
        $message = $email_view_mgr->fetch('_email.bundle-purchase-confirmation.tpl');
        return Mailer::mailHTMLViaMandrillTemplate($email, $subject, $template_name, array('html_body'=>$message),
            $api_key);
    }
}