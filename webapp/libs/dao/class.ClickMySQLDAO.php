<?php
class ClickMySQLDAO extends PDODAO {
    public function insert() {
        $q  = "INSERT INTO clicks (timestamp) VALUES (CURRENT_TIMESTAMP); ";
        $ps = $this->execute($q);
        return $this->getInsertId($ps);

        $sql = "SELECT * FROM clicks WHERE id = ".$result;
        $stmt = TransactionMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual(1, $data['id']);
    }

}