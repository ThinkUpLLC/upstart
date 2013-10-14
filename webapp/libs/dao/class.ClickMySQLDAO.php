<?php
class ClickMySQLDAO extends PDODAO {
    public function insert() {
        $q  = "INSERT INTO clicks (caller_reference_suffix, timestamp) VALUES ";
        $q .= "(:caller_reference_suffix, CURRENT_TIMESTAMP); ";
        $suffix = uniqid();
        $vars = array(
            ':caller_reference_suffix'=>$suffix
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getInsertId($ps).$suffix;
    }

}