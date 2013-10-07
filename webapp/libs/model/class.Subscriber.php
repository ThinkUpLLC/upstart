<?php
class Subscriber {
    /**
     * @var int Internal unique ID.
     */
    var $id;
    /**
     * @var str Subscriber email address.
     */
    var $email;
    /**
     * @var str Subscriber password.
     */
    var $pwd;
    /**
     * @var str Subscriber password salt.
     */
    var $pwd_salt;
    /**
     * @var str Time of subscription.
     */
    var $creation_time;
    /**
     * @var str Subscriber's network user ID.
     */
    var $network_user_id;
    /**
     * @var str Subscriber's network username.
     */
    var $network_user_name;
    /**
     * @var str Subscriber's authorized network, ie, Twitter or Facebook.
     */
    var $network;
    /**
     * @var str Subscriber's full name (as specified on network).
     */
    var $full_name;
    /**
     * @var int Follower or subscriber count of service user.
     */
    var $follower_count;
    /**
     * @var bool Whether or not the service user is verified.
     */
    var $is_verified = false;
    /**
     * @var str OAuth access token for network authorization.
     */
    var $oauth_access_token;
    /**
     * @var str OAuth secret access token for network authorization.
     */
    var $oauth_access_token_secret;
    /**
     * @var int Code for verifying email address.
     */
    var $verification_code;
    /**
     * @var bool Whether or not email address has been verified, 1 or 0.
     */
    var $is_email_verified = false;
    public function __construct($row = false) {
        if ($row) {
            $this->id = $row['id'];
            $this->email = $row['email'];
            $this->pwd = $row['pwd'];
            $this->pwd_salt = $row['pwd_salt'];
            $this->creation_time = $row['creation_time'];
            $this->network_user_id = $row['network_user_id'];
            $this->network_user_name = $row['network_user_name'];
            $this->network = $row['network'];
            $this->full_name = $row['full_name'];
            $this->follower_count = $row['follower_count'];
            $this->is_verified = PDODAO::convertDBToBool($row['is_verified']);
            $this->oauth_access_token = $row['oauth_access_token'];
            $this->oauth_access_token_secret = $row['oauth_access_token_secret'];
            $this->verification_code = $row['verification_code'];
            $this->is_email_verified = PDODAO::convertDBToBool($row['is_email_verified']);
        }
    }
}