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
}