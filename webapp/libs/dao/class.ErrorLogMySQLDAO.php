<?php
class ErrorLogMySQLDAO extends PDODAO {
    public function insert($hash, $location, $debug) {
        $q  = "INSERT INTO error_log (commit_hash, location, debug) VALUES ";
        $q .= "(:commit_hash, :location, :debug); ";
        $vars = array(
            ':commit_hash'=>$hash,
            ':location'=>$location,
            ':debug'=>$debug
        );

        $ps = $this->execute($q, $vars);
        return $this->getInsertId($ps).$suffix;
    }

}