<?php
class SubscriptionOperationMySQLDAO extends PDODAO {

    public function insert(SubscriptionOperation $operation) {
        $q  = "INSERT INTO subscription_operations (subscriber_id, operation, payment_reason, transaction_amount, ";
        $q .= "recurring_frequency, status_code, buyer_email, reference_id, amazon_subscription_id, transaction_date, ";
        $q .= "buyer_name, payment_method ) VALUES ";
        $q .= "(:subscriber_id, :operation, :payment_reason, :transaction_amount, :recurring_frequency, :status_code, ";
        $q .= ":buyer_email, :reference_id, :amazon_subscription_id, FROM_UNIXTIME(:transaction_date), :buyer_name, ";
        $q .= ":payment_method); ";

        $vars = array(
            ':subscriber_id'=>$operation->subscriber_id,
            ':operation'=>$operation->operation,
            ':payment_reason'=>$operation->payment_reason,
            ':transaction_amount'=>$operation->transaction_amount,
            ':recurring_frequency'=>$operation->recurring_frequency,
            ':status_code'=>$operation->status_code,
            ':buyer_email'=>$operation->buyer_email,
            ':reference_id'=>$operation->reference_id,
            ':amazon_subscription_id'=>$operation->amazon_subscription_id,
            ':transaction_date'=>$operation->transaction_date,
            ':buyer_name'=>$operation->buyer_name,
            ':payment_method'=>$operation->payment_method
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        try {
            $ps = $this->execute($q, $vars);
            return $this->getInsertId($ps);
        } catch (PDOException $e) {
            $message = $e->getMessage();
            if (strpos($message,'Duplicate entry') !== false && strpos($message, "for key 'amazon_subscription_id'")
                !== false) {
                throw new DuplicateSubscriptionOperationException($message);
            } else {
                throw new PDOException($message);
            }
        }
    }

    public function getLatestOperation($subscriber_id) {
        $q  = "SELECT * FROM subscription_operations WHERE subscriber_id = :subscriber_id ";
        $q .= "ORDER BY timestamp DESC LIMIT 1; ";

        $vars = array(
            ':subscriber_id'=>$subscriber_id,
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsObject($ps, 'SubscriptionOperation');
    }
}