<?php

class APIIPNController extends UpstartController {
    public function control() {
        $amazon_ipn_utils = new SignatureUtilsForOutbound();

        $ipn_endpoint = Config::getInstance()->getValue('amazon_ipn_endpoint');

        try {
            $debug = '';
            //IPN is sent as a http POST request and hence we specify POST as the http method.
            //Signature verification does not require your secret key
            if ($amazon_ipn_utils->validateRequest($_POST, $ipn_endpoint, "POST")) {
                $debug .= "Signature correct. ";
            } else {
                $debug .= "Signature not correct. ";
            }
            $debug .= Utils::varDumpToString($_POST);

        } catch (Exception $e ) {
            $debug = $e->getMessage();
        }
        $this->logError($debug, __FILE__,__LINE__,__METHOD__);
        //echo $debug;
    }
}