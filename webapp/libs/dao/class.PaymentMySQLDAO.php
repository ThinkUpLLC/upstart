<?php
/**
 * MySQL DAO for managing payment records
 */
class PaymentMySQLDAO extends PDODAO {
    /**
     * Add a new payment
     * @param str $transaction_id transaction id from amazon
     * @param str $request_id request id from amazon
     * @param str $transaction_status transaction status from amazon
     * @param int $amount amount being billed
     * @param str $reference caller referene passed to amazon
     * @return int id of new payment
     */
     public function insert($transaction_id,$request_id,$transaction_status,$amount,$reference,$error_message=null) {
        $q  = "INSERT INTO payments (transaction_id, request_id, transaction_status, error_message, ";
        $q .= "amount, caller_reference) ";
        $q .= "VALUES (:transaction_id, :request_id, :transaction_status, :error_message, :amount, :reference)";

        $cfg = Config::getInstance();
        $vars = array(
            ':transaction_id'=>$transaction_id,
            ':request_id'=>$request_id,
            ':transaction_status'=>$transaction_status,
            ':error_message'=>$error_message,
            ':amount'=>$amount,
            ':reference'=>$reference
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getInsertId($ps);
    }
}
