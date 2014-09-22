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

    public function getBySubscriberID($subscriber_id) {
        $q  = "SELECT so.*, sc.description as status_description FROM subscription_operations so ";
        $q .= "LEFT JOIN subscription_status_codes sc ";
        $q .= "ON sc.code = so.status_code WHERE subscriber_id = :subscriber_id ";
        $q .= "ORDER BY timestamp DESC LIMIT 10; ";

        $vars = array(
            ':subscriber_id'=>$subscriber_id,
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, 'SubscriptionOperation');
    }

    public function getByAmazonSubscriptionID($amazon_subscription_id) {
        $q  = "SELECT so.* FROM subscription_operations so ";
        $q .= "WHERE amazon_subscription_id = :amazon_subscription_id ";
        $q .= "ORDER BY timestamp DESC LIMIT 1; ";

        $vars = array(
            ':amazon_subscription_id'=>$amazon_subscription_id,
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsObject($ps, 'SubscriptionOperation');
    }

    public function getByReferenceID($amazon_subscription_id, $reference_id) {
        $q  = "SELECT so.* FROM subscription_operations so ";
        $q .= "WHERE amazon_subscription_id = :amazon_subscription_id AND reference_id = :reference_id ";
        $q .= "ORDER BY timestamp DESC LIMIT 1; ";

        $vars = array(
            ':amazon_subscription_id'=>$amazon_subscription_id,
            ':reference_id'=>$reference_id,
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsObject($ps, 'SubscriptionOperation');
    }

    /**
     * Calculate the refund a member should receive if the member cancels their subscription today.
     * @param  int $subscriber_id
     * @return int Refund total
     * @throws Exception if last subscription operation was not a payment with a valid amount
     */
    public function calculateProRatedMonthlyRefund($subscriber_id) {
        //Get latest SubscriptionOperation for subscriber
        $last_operation = self::getLatestOperation($subscriber_id);
        if ( $last_operation->operation == 'pay' && $last_operation->recurring_frequency == '1 month' ) {
            // Calculate how many days in month: Between last pay transaction date and a month from it
            $days_in_month = cal_days_in_month(CAL_GREGORIAN, date('n', strtotime($last_operation->transaction_date)),
                date('Y', strtotime($last_operation->transaction_date)));
            //debug
//             echo "Days in month ".$days_in_month."
// ";
            // How much per day: cost per month / days in the month
            $cost_per_month = intval(str_replace('USD ', '', $last_operation->transaction_amount));
            //debug
//             echo "Cost per month ".$cost_per_month."
// ";
            if ($cost_per_month > 1) {
                $cost_per_day = ($cost_per_month / $days_in_month);
                //debug
//                 echo "Cost per day ".$cost_per_day."
// ";
                // How many days to refund: Month from last pay transaction minus today
                $next_transaction_date = strtotime('next month', strtotime($last_operation->transaction_date));
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
                throw new Exception('Invalid cost per month calculated from '. $last_operation->transaction_amount);
            }
        } else {
            // last operation wasn't a monthly payment
            throw new Exception('Last operation wasn\'t a monthly recurring payment it was operation: '.
                $last_operation->operation ." recurring frequency: ".$last_operation->recurring_frequency);
        }
    }

    /**
     * Get last three days worth of successful payments and refunds - total, sum, and date.
     * @return array
     */
    public function getDailyRevenue() {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime("-1 days"));
        $day_before = date('Y-m-d', strtotime("-2 days"));
        $results = array(
            $today => array('successful_payments'=>0, 'revenue'=>0, 'refunds'=>0),
            $yesterday => array('successful_payments'=>0, 'revenue'=>0, 'refunds'=>0),
            $day_before =>  array('successful_payments'=>0, 'revenue'=>0, 'refunds'=>0),
        );

        $q = "SELECT operation, transaction_amount, DATE(timestamp) AS date, reference_id ";
        $q .= "FROM subscription_operations so WHERE ";
        $q .= "( date(timestamp) = '".$today."' ";
        $q .= "OR date(timestamp) = '".$yesterday."' ";
        $q .= "OR date(timestamp) = '".$day_before."') ";
        $q .= "ORDER BY timestamp DESC;";

        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        $revenue_results = $this->getDataRowsAsArrays($ps);
        foreach ($revenue_results as $rev) {
            if ($rev['date'] == $today) {
                if ($rev['operation'] == 'pay') {
                    $results[$today]['successful_payments'] = $results[$today]['successful_payments'] + 1;
                    $results[$today]['revenue'] =
                        $results[$today]['revenue'] + intval(str_replace('USD ', '', $rev['transaction_amount']));
                } elseif ($rev['operation'] == 'refund') {
                    $results[$today]['refunds'] = $results[$today]['refunds'] + 1;
                }
            } elseif ($rev['date'] == $yesterday) {
                if ($rev['operation'] == 'pay') {
                    $results[$yesterday]['successful_payments'] = $results[$yesterday]['successful_payments'] + 1;
                    $results[$yesterday]['revenue'] =
                        $results[$yesterday]['revenue'] + intval(str_replace('USD ', '', $rev['transaction_amount']));
                } elseif ($rev['operation'] == 'refund') {
                    $results[$yesterday]['refunds'] = $results[$yesterday]['refunds'] + 1;
                }
            } elseif ($rev['date'] == $day_before) {
                if ($rev['operation'] == 'pay') {
                    $results[$day_before]['successful_payments'] = $results[$day_before]['successful_payments'] + 1;
                    $results[$day_before]['revenue'] =
                        $results[$day_before]['revenue'] + intval(str_replace('USD ', '', $rev['transaction_amount']));
                } elseif ($rev['operation'] == 'refund') {
                    $results[$day_before]['refunds'] = $results[$day_before]['refunds'] + 1;
                }
            }
        }
        return $results;
    }
}