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
     * Get payments for a given subscriber
     * @param int subscriber_id
     * @return array
     */
    public function getBySubscriber($subscriber_id) {
        $q = 'SELECT p.timestamp, p.transaction_id, p.request_id, p.transaction_status, p.error_message, p.amount,  ';
        $q.= 'p.caller_reference ';
        $q.= 'FROM subscriber_payments sp LEFT JOIN payments p ON (p.id = sp.payment_id) ';
        $q.= 'WHERE sp.subscriber_id=:subscriber_id ';
        $q.= 'ORDER BY timestamp ASC';
        $vars = array(':subscriber_id' => $subscriber_id);

        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->fetchAllAndClose($ps);
    }
}
