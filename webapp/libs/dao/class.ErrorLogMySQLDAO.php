<?php
class ErrorLogMySQLDAO extends PDODAO {
    public function insert($hash, $filename, $line_number, $method, $debug) {
        $q  = "INSERT INTO error_log (commit_hash, filename, line_number, method, debug) VALUES ";
        $q .= "(:commit_hash, :filename, :line_number, :method, :debug); ";
        $vars = array(
            ':commit_hash'=>$hash,
            ':filename'=>$filename,
            ':line_number'=>$line_number,
            ':method'=>$method,
            ':debug'=>$debug
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getInsertId($ps);
    }

    public function getErrorList($page_number=1, $count=50) {
        $start_on_record = ($page_number - 1) * $count;
        $q  = "SELECT * FROM error_log e ";
        $q .= "ORDER BY timestamp DESC ";
        $q .= "LIMIT :start_on_record, :limit;";

        $vars = array(
            ':start_on_record'=>$start_on_record,
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    public function getErrorsByMethod($method, $page_number=1, $count=50) {
        $start_on_record = ($page_number - 1) * $count;
        $q  = "SELECT * FROM error_log e WHERE method = :method ";
        $q .= "ORDER BY timestamp DESC ";
        $q .= "LIMIT :start_on_record, :limit;";

        $vars = array(
            ':method'=>$method,
            ':start_on_record'=>$start_on_record,
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }
}