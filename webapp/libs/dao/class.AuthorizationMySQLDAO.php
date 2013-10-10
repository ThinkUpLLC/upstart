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
        $vars = array(
            ':token_id'=>$token_id,
            ':amount'=>$amount,
            ':status_code'=>$status_code,
            ':error_message'=>$error_message,
            ':payment_method_expiry'=>$payment_method_expiry,
            ':caller_reference'=>$caller_reference,
            ':token_validity_start_date'=>'2014-01-01 00:00:00' //@TODO Stop hardcoding this
        );
        //echo self::mergeSQLVars($q, $vars);
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
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsObject($ps, "Authorization");
    }

    private static function mergeSQLVars($sql, $vars) {
        foreach ($vars as $k => $v) {
            $sql = str_replace($k, (is_int($v))?$v:"'".$v."'", $sql);
        }
        $config = Config::getInstance();
        $prefix = $config->getValue('table_prefix');
        $gmt_offset = $config->getGMTOffset();
        $sql = str_replace('#gmt_offset#', $gmt_offset, $sql);
        $sql = str_replace('#prefix#', $prefix, $sql);
        return $sql;
    }

    public function getTotalAuthorizations() {
        $q  = "SELECT SUM(amount) as total FROM authorizations;";
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }
}