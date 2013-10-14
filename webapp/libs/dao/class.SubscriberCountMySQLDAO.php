<?php
class SubscriberCountMySQLDAO extends PDODAO {
    public function increment($amount) {
        $q  = "UPDATE subscriber_counts SET count = (count + 1) WHERE amount = :amount;";
        $vars = array(
            ':amount'=>$amount
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        $update_count = $this->getUpdateCount($ps);
        if ($update_count < 1) {
            $q  = "INSERT INTO subscriber_counts (amount, count) VALUES (:amount, 1);";
            if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
            $ps = $this->execute($q, $vars);
            return $this->getUpdateCount($ps);
        } else {
            return $update_count;
        }
    }

    public function getAll() {
        $q  = "SELECT * FROM subscriber_counts UNION SELECT 'all', sum(count) FROM subscriber_counts";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        $rows = $this->getDataRowsAsArrays($ps);
        $counts = array();
        foreach ($rows as $row) {
            $counts[$row['amount']] = $row['count'];
        }
        return $counts;
    }
}