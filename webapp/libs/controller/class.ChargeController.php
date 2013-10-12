<?php
/**
 * Create interface to Amazon Flexible Payment System
 */
class ChargeController extends Controller {
    public function control() {
        $this->setViewTemplate('admin-charge.tpl');
        if (isset($_GET['token_id']) && $_GET['amount']) {
            self::invokeAmazonPayAction($_GET['token_id'], $_GET['amount']);
        }
        return $this->generateView();
    }

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

    private static function getAmazonFPSURL($caller_reference, $callback_url, $amount) {
        $cfg = Config::getInstance();
        $AWS_ACCESS_KEY_ID = $cfg->getValue('AWS_ACCESS_KEY_ID');
        $AWS_SECRET_ACCESS_KEY = $cfg->getValue('AWS_SECRET_ACCESS_KEY');
        $amazon_payment_auth_validity_start = $cfg->getValue('amazon_payment_auth_validity_start');

        $pipeline = new Amazon_FPS_CBUIRecurringTokenPipeline($AWS_ACCESS_KEY_ID, $AWS_SECRET_ACCESS_KEY);

        $pipeline->setMandatoryParameters($caller_reference, $callback_url, $amount, "12 Months");

        //optional parameters
        $pipeline->addParameter("paymentReason", "ThinkUp monthly subscription");
        $pipeline->addParameter("validityStart", $amazon_payment_auth_validity_start);
        $pipeline->addParameter("cobrandingUrl",
        UpstartHelper::getApplicationURL(false, false, false)."assets/img/thinkup-logo-transparent.png");
        $pipeline->addParameter("websiteDescription", "ThinkUp");

        return $pipeline->getUrl();
    }
}
