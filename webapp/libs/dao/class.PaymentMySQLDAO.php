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
     * Calculate the refund an annual member should receive if the member cancels their subscription today.
     * @param  arr $payment
     * @return int Refund total
     * @throws Exception if last payment was not a payment with a valid amount
     */
    public function calculateProRatedAnnualRefund($payment) {
        $days_in_year = 365;
        // How much per day: cost per month / days in the month
        $cost_per_year = $payment['amount'];
        if ($cost_per_year > 1) {
            $cost_per_day = ($cost_per_year / $days_in_year);
            //debug
//                 echo "Cost per day ".$cost_per_day."
// ";
            // How many days to refund: Month from last pay transaction minus today
            $next_transaction_date = strtotime('+365 day', strtotime($payment['timestamp']));
            //debug
//                 echo "Next transaction date ".date('M-d-Y', $next_transaction_date)."
// ";
            $days_to_refund = ($next_transaction_date - time()) / (60*60*24);
            //debug
//                 echo "Days to refund ".$days_to_refund."
// ";
            // Refund total: How many days to refund * how much per day
            $refund_total = round( ($days_to_refund * $cost_per_day), 2);
            return $refund_total;
        } else {
            //Invalid cost per month
            throw new Exception('Invalid cost per year calculated from '. $payment['timestamp']);
        }
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
     * Set refund details for a payment.
     * @param int $id
     * @param str $refund_date
     * @param str $refund_caller_reference
     * @param int $refund_amount
     * @return int Number of rows updated
     */
    public function setRefund($id, $refund_caller_reference, $refund_amount, $refund_date = 'NOW()') {
        $q  = "UPDATE payments SET refund_date=:refund_date, refund_caller_reference=:refund_caller_reference, ";
        $q .= "refund_amount = :refund_amount WHERE id = :id; ";

        $vars = array(
            ':id'=>$id,
            ':refund_date'=>(($refund_date=='NOW()')?date('Y-m-d H:m:s'):$refund_date),
            ':refund_caller_reference'=>$refund_caller_reference,
            ':refund_amount'=>$refund_amount
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
