<?php

class ClaimCode {
    /**
     * @var int Internal, unique ID.
     */
    var $id;
    /**
     * @var str Unique, user-facing claim code.
     */
    var $code;
    /**
     * @var str Purchase association - bundle, gift, etc.
     */
    var $type;
    /**
     * @var str Caller reference for transaction.
     */
    var $operation_id;
    /**
     * @var bool Whether or not the code is redeemed.
     */
    var $is_redeemed = false;
    /**
     * @var str When the code was redeemed.
     */
    var $redemption_date;
    /**
     * @var int How many days of membership this code represents.
     */
    var $number_days;
    public function __construct($row = false) {
        if ($row) {
            $this->id = $row['id'];
            $this->code = $row['code'];
            $this->type = $row['type'];
            $this->operation_id = $row['operation_id'];
            $this->is_redeemed = PDODAO::convertDBToBool($row['is_redeemed']);
            $this->redemption_date = $row['redemption_date'];
            $this->number_days = $row['number_days'];
        }
    }
}
