<?php
class ClaimCodeOperationMySQLDAO extends PDODAO {
    public function insert($reference_id, $transaction_id, $buyer_email, $buyer_name, $transaction_amount, $status_code,
        $type, $number_days) {
        $q  = "INSERT INTO claim_code_operations (reference_id, transaction_id, buyer_email, buyer_name, ";
        $q .= "transaction_amount, status_code, type, number_days) VALUES ";
        $q .= "(:reference_id, :transaction_id, :buyer_email, :buyer_name, :transaction_amount, :status_code, :type, ";
        $q .= ":number_days); ";
        $vars = array(
            ':reference_id'=>$reference_id,
            ':transaction_id'=>$transaction_id,
            ':buyer_email'=>$buyer_email,
            ':buyer_name'=>$buyer_name,
            ':transaction_amount'=>$transaction_amount,
            ':status_code'=>$status_code,
            ':type'=>$type,
            ':number_days'=>$number_days,
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        try {
            $ps = $this->execute($q, $vars);
        //Catch duplicate key exception and try again with new code
        } catch (PDOException $e) {
            if (!preg_match("/Duplicate entry .* for key 'amazon_reference_id'/", $e->getMessage())) {
                throw $e;
            } else {
                throw new DuplicateClaimCodeOperationException();
            }
        }
        return $this->getInsertId($ps);
    }

    public function getByReferenceID($reference_id) {
        $q = "SELECT * FROM claim_code_operations WHERE reference_id = :reference_id";
        $vars = array ( ':reference_id' => $reference_id);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsObject($ps, "ClaimCodeOperation");
    }
}