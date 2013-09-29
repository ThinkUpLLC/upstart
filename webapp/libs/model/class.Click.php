<?php

class Click {
    /**
     * @var int Internal unique ID.
     */
    var $id;
    /**
     * @var str Time of click.
     */
    var $timestamp;
    public function __construct($row = false) {
        if ($row) {
            $this->id = $row['id'];
            $this->timestamp = $row['timestamp'];
        }
    }
}
