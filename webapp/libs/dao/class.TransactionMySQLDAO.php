<?php
class TransactionMySQLDAO extends PDODAO {
    public function insert($token_id, $amount, $status_code, $error_message=null, $payment_method_expiry=null) {
        if (!in_array($status_code, Transaction::$status_codes)) {
            throw new InvalidTransactionStatusCodeException($status_code . " is not a valid status code.");
        }
        $q  = "INSERT INTO transactions (token_id, amount, status_code, error_message, payment_method_expiry) ";
        $q .= "VALUES (:token_id, :amount, :status_code, :error_message, :payment_method_expiry); ";
        $vars = array(
            ':token_id'=>$token_id,
            ':amount'=>$amount,
            ':status_code'=>$status_code,
            ':error_message'=>$error_message,
            ':payment_method_expiry'=>$payment_method_expiry
        );
        try {
            $ps = $this->execute($q, $vars);
        } catch (PDOException $e) {
            $message = $e->getMessage();
            if (strpos($message,'Duplicate entry') !== false) {
                throw new DuplicateTransactionException($message);
            } else {
                throw new PDOException($message);
            }
        }
        return $this->getInsertId($ps);
    }

}