<?php

class UpstartTestDatabaseHelper extends PDODAO {

    /**
     * Create tables
     */
    public function create($script_path) {
        error_reporting(22527); //Don't show E_DEPRECATED PHP messages, split() is deprecated
        //Create all the tables based on the build script
        $create_db_script = file_get_contents($script_path);
        $create_statements = split(";", $create_db_script);
        foreach ($create_statements as $q) {
            if (trim($q) != '') {
                self::execute($q);
            }
        }
    }

    /**
     * Drop tables
     */
    public function drop($db) {
        //Delete test data by dropping all existing tables
        $q = "SHOW TABLES FROM ".$db;
        $stmt = self::execute($q);
        $results = $this->getDataRowsAsArrays($stmt);
        foreach ($results as $result) {
            $q = "DROP TABLE ".$result['Tables_in_'.$db];
            self::execute($q);
        }
    }

    /**
     * Exposing protected execute method for direct use by tests only
     * @param $sql
     */
    public function runSQL($sql) {
        return self::execute($sql);
    }

    /**
     * Check if a database exists
     * @param str $db_name
     * @return bool
     */
    public function databaseExists($db_name) {
        $config = Config::getInstance();
        $db_string = $this->getDBString();

        $tempPDO = new PDO($db_string, $config->getValue('db_user'), $config->getValue('db_password'));
        $sql =  "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".$db_name."';";
        $stmt = $tempPDO->prepare($sql);
        $stmt->execute();
        $row_count = $stmt->rowCount();
        return ($row_count > 0)?true:false;
    }

    /**
     * Drop a database.
     * @param str $db_name
     */
    public function deleteDatabase($db_name) {
        $config = Config::getInstance();
        $db_string = $this->getDBString();

        $tempPDO = new PDO($db_string, $config->getValue('db_user'), $config->getValue('db_password'));
        $stmt = $tempPDO->prepare('DROP DATABASE `'.$db_name.'`;');
        $stmt->execute();
    }
    /**
     * Get PDO connection string.
     * @return str
     */
    private function getDBString() {
        $config = Config::getInstance();
        $server = $config->getValue('db_host');
        if ($config->getValue('db_port')) {
            $server .= ':'.$config->getValue('db_port');
        }
        $db_type = $config->getValue('db_type');

        if(!$db_type) {
            $db_type = 'mysql';
        }
        $db_socket = $config->getValue('db_socket');

        if (!$db_socket) {
            $db_port = $config->getValue('db_port');
            if (!$db_port) {
                $db_socket = '';
            } else {
                $db_socket = ";port=".$config->getValue('db_port');
            }
        } else {
            $db_socket=";unix_socket=".$db_socket;
        }
        $db_string = sprintf( "%s:host=%s%s", $db_type, $config->getValue('db_host'), $db_socket );
        return $db_string;
    }
}
