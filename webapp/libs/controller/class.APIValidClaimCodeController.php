<?php
/**
 * Static implementation for demo
 */
/*
if (isset($_GET['code'])) {
    if (strtolower($_GET['code']) == strtolower('1234567890AB')) {
        $response = array('code'=>'1234567890AB', 'is_valid'=>true);
    } else {
        if (strlen($_GET["code"]) == 12 && preg_match('[a-zA-Z0-9]$', $_GET['code']) !== 0 ) {
            $response = array('code'=>$_GET['code'], 'is_valid'=>false);
        } else {
            $response = array('error'=>"Not a valid code format");
        }
    }
} else {
    $response = array('error'=>"Error message which is both specific and confounding will be here");
}

header('Content-Type: application/json', true);
print_r(json_encode($response));
*/
class APIValidClaimCodeController extends Controller {

    public function control() {
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            if (strlen($code) == 12 && !preg_match('/[^a-zA-Z0-9]/i', $code)) {
                $claim_code_dao = new ClaimCodeMySQLDAO();
                $code_object = $claim_code_dao->get(strtoupper($code));
                $is_valid = false;
                if (isset($code_object)) {
                    $is_valid = true;
                }
                $response = array('code'=>$code, 'is_valid'=>$is_valid);
            } else {
                $response = array('error'=>"Not a valid code format");
            }
        } else {
            $response = array('error'=>"No code specified");
        }
        $this->setJsonData($response);
        return $this->generateView();
    }
}