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
     * Get Payments with a Pending status.
     * @param int $limit
     * @return arr Payment objects
     */
    public function getPendingPayments($limit=50) {
        $q  = "SELECT p.*, sp.subscriber_id FROM payments p ";
        $q .= "INNER JOIN subscriber_payments sp ON sp.payment_id = p.id ";
        $q .= "WHERE p.transaction_status = 'Pending' LIMIT :limit;";
        $vars = array(':limit'=>$limit);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, 'Payment');
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
        $payment = $this->getDataRowAsObject($ps, 'Payment');
        if (!isset($payment)) {
            throw new PaymentDoesNotExistException('Payment '.$transaction_id.', caller reference '.$caller_reference.
                ' does not exist.');
        }
        return $payment;
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
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime("-1 days"));
        $day_before = date('Y-m-d', strtotime("-2 days"));
        $results = array(
            $today => array('successful_payments'=>0, 'revenue'=>0),
            $yesterday => array('successful_payments'=>0, 'revenue'=>0),
            $day_before =>  array('successful_payments'=>0, 'revenue'=>0),
        );

        $q = "SELECT count(id) as successful_payments, SUM(amount) as revenue, ";
        $q .= "DATE(timestamp) AS date  FROM payments WHERE transaction_status = 'Success' ";
        $q .= "AND ( date(timestamp) = '".$today."' ";
        $q .= "OR date(timestamp) = '".$yesterday."' ";
        $q .= "OR date(timestamp) = '".$day_before."') ";
        $q .= "GROUP BY DATE(timestamp) ORDER BY timestamp DESC;";

        $ps = $this->execute($q);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        $revenue_results = $this->getDataRowsAsArrays($ps);
        foreach ($revenue_results as $rev) {
            if ($rev['date'] == $today) {
                $results[$today]['successful_payments'] = $rev['successful_payments'];
                $results[$today]['revenue'] = $rev['revenue'];
            } elseif ($rev['date'] == $yesterday) {
                $results[$yesterday]['successful_payments'] = $rev['successful_payments'];
                $results[$yesterday]['revenue'] = $rev['revenue'];
            } elseif ($rev['date'] == $day_before) {
                $results[$day_before]['successful_payments'] = $rev['successful_payments'];
                $results[$day_before]['revenue'] = $rev['revenue'];
            }
        }
        return $results;
    }
}
