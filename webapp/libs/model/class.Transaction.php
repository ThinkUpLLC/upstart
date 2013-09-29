<?php
class Transaction {
    /**
     * @var int Internal unique ID.
     */
    var $id;
    /**
     * @var str Time of transaction.
     */
    var $timestamp;
    /**
     * @var str Token ID of transaction.
     */
    var $token_id;
    /**
     * @var int Monetary amount of transaction in US Dollars.
     */
    var $amount;
    /**
     * @var date Expiration date of transaction.
     */
    var $expiry;
    public function __construct($row = false) {
        if ($row) {
            $this->id = $row['id'];
            $this->timestamp = $row['timestamp'];
            $this->token_id = $row['token_id'];
            $this->amount = $row['amount'];
            $this->expiry = $row['expiry'];
        }
    }
}