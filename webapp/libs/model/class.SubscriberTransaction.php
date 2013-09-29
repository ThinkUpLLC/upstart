<?php
class SubscriberTransaction {
    /**
     * @var int Internal unique ID.
     */
    var $id;
    /**
     * @var str Time transaction was recorded.
     */
    var $timestamp;
    /**
     * @var int Subscriber ID keyed to subscribers.
     */
    var $subscriber_id;
    /**
     * @var int Transaction ID keyed to transactions.
     */
    var $transaction_id;
    public function __construct($row = false) {
        if ($row) {
            $this->id = $row['id'];
            $this->timestamp = $row['timestamp'];
            $this->subscriber_id = $row['subscriber_id'];
            $this->transaction_id = $row['transaction_id'];
        }
    }
}