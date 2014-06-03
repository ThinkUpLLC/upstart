<?php
/**
 * MySQL DAO for managing payment records
 */
class PaymentMySQLDAO extends PDODAO {
    /**
     * Add a new payment.
     * @param str $transaction_id transaction id from amazon
     * @param str $request_id request id from amazon
     * @param str $transaction_status transaction status from amazon
     * @param int $amount amount being billed
     * @param str $reference caller referene passed to amazon
     * @return int id of new payment
     */
     public function insert($transaction_id, $request_id, $transaction_status, $amount, $reference,
    $status_message=null) {
        $q  = "INSERT INTO payments (transaction_id, request_id, transaction_status, status_message, ";
        $q .= "amount, caller_reference) ";
        $q .= "VALUES (:transaction_id, :request_id, :transaction_status, :status_message, :amount, :reference)";

        $cfg = Config::getInstance();
        $vars = array(
            ':transaction_id'=>$transaction_id,
            ':request_id'=>$request_id,
            ':transaction_status'=>$transaction_status,
            ':status_message'=>$status_message,
            ':amount'=>$amount,
            ':reference'=>$reference
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getInsertId($ps);
    }
    /**
     * Get the sum of all successful payments by active subscribers.
     * @return int
     */
    public function getTotalPayments() {
        $q  = "SELECT SUM(amount) as total FROM payments p ";
        $q .= "INNER JOIN subscriber_payments sp ON p.id = sp.payment_id ";
        $q .= "INNER JOIN subscribers s on sp.subscriber_id = s.id ";
        $q .= "WHERE transaction_status = 'Success';";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }
    /**
     * Get the count of all payments with a Pending status.
     * @return int
     */
    public function getTotalPendingPayments() {
        $q  = "SELECT count(*) as count FROM payments p ";
        $q .= "WHERE transaction_status = 'Pending';";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['count'];
    }
    /**
     * Get transaction IDs of payments with a Pending status.
     * @param int $limit
     * @return arr
     */
    public function getPendingPayments($limit) {
        $q  = "SELECT p.transaction_id, sp.subscriber_id FROM payments p ";
        $q .= "INNER JOIN subscriber_payments sp ON sp.payment_id = p.id ";
        $q .= "WHERE p.transaction_status = 'Pending' LIMIT :limit;";
        $vars = array(':limit'=>$limit);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }
    /**
     * Get payment by transaction ID and caller reference.
     * @param str $transaction_id
     * @param str $caller_reference
     * @return Payment $payment
     */
    public function getPayment($transaction_id, $caller_reference) {
        $q  = "SELECT * FROM payments p ";
        $q .= "WHERE transaction_id = :transaction_id AND caller_reference = :caller_reference; ";

        $vars = array(
            ':transaction_id'=>$transaction_id,
            ':caller_reference'=>$caller_reference
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsObject($ps, 'Payment');
    }
    /**
     * Update payment status.
     * @param int $id Payment ID
     * @param str $transaction_status Short status
     * @param str $status_message Status explainer
     * @return int Number of payments updated
     */
    public function updateStatus($id, $transaction_status, $status_message) {
        $q  = "UPDATE payments SET transaction_status=:transaction_status, status_message=:status_message ";
        $q .= "WHERE id = :id; ";

        $vars = array(
            ':id'=>$id,
            ':transaction_status'=>$transaction_status,
            ':status_message'=>$status_message
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    /**
     * Get last three days worth of successful payments - total, sum, and date.
     * @return array
     */
    public function getDailyRevenue() {
        $q = "SELECT count(id) as successful_payments, SUM(amount) as revenue, ";
        $q .= "DATE(timestamp) AS date  FROM payments WHERE transaction_status = 'Success' ";
        $q .= "GROUP BY DATE(timestamp) ORDER BY timestamp DESC LIMIT 3;";
        $ps = $this->execute($q);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        return $this->getDataRowsAsArrays($ps);
    }
}
