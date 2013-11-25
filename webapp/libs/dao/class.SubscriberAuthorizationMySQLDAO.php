<?php
class SubscriberAuthorizationMySQLDAO extends PDODAO {
    public function insert($subscriber_id, $token_id ) {
        $q  = "INSERT INTO subscriber_authorizations (subscriber_id, authorization_id) VALUES ";
        $q .= "(:subscriber_id, :authorization_id); ";
        $vars = array(
            ':subscriber_id'=>$subscriber_id,
            ':authorization_id'=>$token_id
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        try {
            $ps = $this->execute($q, $vars);
        } catch (PDOException $e) {
            $message = $e->getMessage();
            if (strpos($message,'Duplicate entry') !== false) {
                throw new DuplicateSubscriberAuthorizationException($message);
            } else {
                throw new PDOException($message);
            }
        }
        return $this->getInsertId($ps);
    }

    public function deleteBySubscriberID($subscriber_id) {
        $q  = "DELETE FROM subscriber_authorizations WHERE subscriber_id = :subscriber_id";
        $vars = array(
            ':subscriber_id'=>$subscriber_id
        );
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function getBySubscriberID($subscriber_id) {
        $q  = "SELECT *, UNIX_TIMESTAMP(a.token_validity_start_date) as token_validity_start_date_ts ";
        $q .= "FROM authorizations a INNER JOIN subscriber_authorizations sa ON a.id = sa.authorization_id ";
        $q .= "LEFT JOIN authorization_status_codes sc ON sc.code = a.status_code ";
        $q .= "WHERE subscriber_id = :subscriber_id";
        $vars = array(
            ':subscriber_id'=>$subscriber_id
        );
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, "Authorization");
    }

}