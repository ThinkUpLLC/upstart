<?php
class ClaimCodeMySQLDAO extends PDODAO {
    public function insert($type, $operation_id, $number_days, $code = null) {
        $retry = true;
        while ($retry) {
            $q  = "INSERT INTO claim_codes (code, type, operation_id, number_days) VALUES ";
            $q .= "(:code, :type, :operation_id, :number_days); ";
            if (!isset($code)) {
                $code = self::generateCode();
            }
            $vars = array(
                ':code'=>$code,
                ':type'=>$type,
                ':operation_id'=>$operation_id,
                ':number_days'=>$number_days
            );
            if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
            try {
                $ps = $this->execute($q, $vars);
                $retry = false;
            //Catch duplicate key exception and try again with new code
            } catch (PDOException $e) {
                if (!preg_match("/Duplicate entry .* for key 'code'/", $e->getMessage())) {
                    throw $e;
                } else {
                    $code = null;
                }
            }
        }
        return $code;
    }

    public function getClaimCodeList($page_number=1, $count=50) {
        $start_on_record = ($page_number - 1) * $count;
        $q  = "SELECT c.*, co.* FROM claim_codes c ";
        $q .= "INNER JOIN claim_code_operations co ON co.id = c.operation_id ";
        $q .= "ORDER BY co.timestamp DESC ";
        $q .= "LIMIT :start_on_record, :limit;";

        $vars = array(
            ':start_on_record'=>$start_on_record,
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        $rows = $this->getDataRowsAsArrays($ps);
        //Hack: Overload the claim code object with fields from operations table
        $claim_codes = array();
        foreach ($rows as $row) {
            $claim_code = new ClaimCode($row);
            $claim_code->readable_code = self::makeClaimCodeReadable($row['code']);
            $claim_code->email = $row['buyer_email'];
            $claim_code->timestamp = $row['timestamp'];
            $claim_codes[] = $claim_code;
        }
        return $claim_codes;
    }

    /**
     * Generate 12-character alphanumeric claim code.
     * With a little help from our friends at Stack:
     * http://stackoverflow.com/questions/22333237/generating-unique-hard-to-guess-coupon-codes
     * @return str Code
     */
    public static function generateCode() {
        $long_code = strtr(md5(uniqid(rand())), '0123456789abcdefghij', '234679QWERTYUPADFGHX');
        return substr($long_code, 0, 12);
    }
    /**
     * Output a 12-character code with a space every 4 characters for readability.
     * @param  str $code
     * @return str
     */
    public static function makeClaimCodeReadable($code) {
        return substr($code, 0, 4)." ".substr($code, 4, 4). " ".substr($code, 8, 4);
    }
    /**
     * Get a ClaimCode object by the code string.
     * @param  str $code
     * @return ClaimCode
     */
    public function get($code) {
        $q = "SELECT * FROM claim_codes WHERE code = :code";
        $vars = array ( ':code' => $code);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsObject($ps, "ClaimCode");
    }
    /**
     * Mark a claim code as redeemed in the data store.
     * @param  str $code
     * @return int
     */
    public function redeem($code) {
        $q = "UPDATE claim_codes SET redemption_date = CURRENT_TIMESTAMP, is_redeemed = 1 WHERE code = :code";
        $vars = array ( ':code' => $code);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }
}