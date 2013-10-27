<?php
class AuthorizationMySQLDAO extends PDODAO {
    public function insert($token_id, $amount, $status_code, $caller_reference, $error_message=null,
    $payment_method_expiry=null) {
        if (!in_array($status_code, Authorization::$status_codes)) {
            throw new InvalidAuthorizationStatusCodeException($status_code . " is not a valid status code.");
        }
        $q  = "INSERT INTO authorizations (token_id, amount, status_code, error_message, payment_method_expiry, ";
        $q .= "caller_reference, token_validity_start_date) ";
        $q .= "VALUES (:token_id, :amount, :status_code, :error_message, :payment_method_expiry, :caller_reference, ";
        $q .= ":token_validity_start_date);";

        $cfg = Config::getInstance();
        $token_validity_start_date = $cfg->getValue('amazon_payment_auth_validity_start');
        $vars = array(
            ':token_id'=>$token_id,
            ':amount'=>$amount,
            ':status_code'=>$status_code,
            ':error_message'=>$error_message,
            ':payment_method_expiry'=>$payment_method_expiry,
            ':caller_reference'=>$caller_reference,
            ':token_validity_start_date'=>date("Y-m-d H:i:s",$token_validity_start_date)
        );
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        try {
            $ps = $this->execute($q, $vars);
        } catch (PDOException $e) {
            $message = $e->getMessage();
            if (strpos($message,'Duplicate entry') !== false) {
                throw new DuplicateAuthorizationException($message);
            } else {
                throw new PDOException($message);
            }
        }
        return $this->getInsertId($ps);
    }

    public function getByTokenID($token_id) {
        $q = "SELECT * FROM authorizations WHERE token_id = :token_id";
        $vars = array ( ':token_id' => $token_id);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsObject($ps, "Authorization");
    }

    public function getTotalAuthorizations() {
        $q  = "SELECT SUM(amount) as total FROM authorizations a ";
        $q .= "INNER JOIN subscriber_authorizations sa ON sa.authorization_id = a.id;";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }
}