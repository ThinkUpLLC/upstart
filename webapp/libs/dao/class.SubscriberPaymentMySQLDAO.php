<?php
/**
 * MySQL DAO for managing subcriber to payment joining records
 */
class SubscriberPaymentMySQLDAO extends PDODAO {
    /**
     * Link a payment to a subscriber
     * @param str $subscriber_id id of subscriber
     * @param str $payment_id id of payment
     * @return int id of new link
     */
    public function insert($subscriber_id, $payment_id) {
        $q  = "INSERT INTO subscriber_payments (subscriber_id, payment_id) VALUES ";
        $q .= "(:subscriber_id, :payment_id); ";
        $vars = array(
            ':subscriber_id'=>$subscriber_id,
            ':payment_id'=>$payment_id
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        try {
            $ps = $this->execute($q, $vars);
        } catch (PDOException $e) {
            $message = $e->getMessage();
            if (strpos($message,'Duplicate entry') !== false) {
                throw new DuplicateSubscriberPaymentException($message);
            } else {
                throw new PDOException($message);
            }
        }
        return $this->getInsertId($ps);
    }

    /**
     * Get payments for a given subscriber.
     * @param int $subscriber_id
     * @param int $limit Default to null (no limit)
     * @return arr
     */
    public function getBySubscriber($subscriber_id, $limit=null) {
        $q = 'SELECT p.id, p.timestamp, p.transaction_id, p.request_id, p.transaction_status, p.status_message, ';
        $q.= 'p.amount, p.caller_reference, p.refund_amount, p.refund_date, p.refund_caller_reference ';
        $q.= 'FROM subscriber_payments sp LEFT JOIN payments p ON (p.id = sp.payment_id) ';
        $q.= 'WHERE sp.subscriber_id=:subscriber_id ';
        $q.= 'ORDER BY timestamp DESC ';
        $vars = array(
            ':subscriber_id' => $subscriber_id
        );
        if ($limit != null) {
            $q .= "LIMIT :limit";
            $vars[':limit'] = $limit;
        }
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }
}
