<?php
class ClickMySQLDAO extends PDODAO {
    public function insert() {
        $q  = "INSERT INTO clicks (timestamp) VALUES (CURRENT_TIMESTAMP); ";
        $ps = $this->execute($q);
        return $this->getInsertId($ps);
    }

}