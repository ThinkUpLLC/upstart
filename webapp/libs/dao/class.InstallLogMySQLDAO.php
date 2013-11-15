<?php
class InstallLogMySQLDAO extends PDODAO {

    public function insertLogEntry($subscriber_id, $commit_hash, $success, $migration_message) {
        $q = "INSERT INTO install_log (subscriber_id, commit_hash, migration_success, migration_message) VALUES ";
        $q .= "(:subscriber_id, :commit_hash, :migration_success, :migration_message ); ";

        $vars = array(
            ':subscriber_id'=>$subscriber_id,
            ':commit_hash'=>$commit_hash,
            ':migration_success'=>$success,
            ':migration_message'=>$migration_message,
        );
        if ($this->profiler_enabled) Profiler::setDAOMethod(__METHOD__);
        $ps = $this->execute($q, $vars);
        return $this->getInsertId($ps);
    }
}
