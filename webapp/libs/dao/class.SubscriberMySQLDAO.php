<?php
class SubscriberMySQLDAO extends PDODAO {
    public function insert($email, $pwd ) {
        $verification_code = rand(1000, 9999);
        $pwd_salt = $this->generateSalt($email);
        $hashed_pwd = $this->hashPassword($pwd, $pwd_salt);

        $q  = "INSERT INTO subscribers (email, pwd, pwd_salt, creation_time, verification_code) VALUES ";
        $q .= "(:email, :pwd, :pwd_salt, CURRENT_TIMESTAMP, :verification_code); ";
        $vars = array(
            ':email'=>$email,
            ':pwd'=>$hashed_pwd,
            ':pwd_salt'=>$pwd_salt,
            ':verification_code'=>$verification_code
        );

        try {
            $ps = $this->execute($q, $vars);
            return $this->getInsertId($ps);
        } catch (PDOException $e) {
            $message = $e->getMessage();
            if (strpos($message,'Duplicate entry') !== false && strpos($message,'email') !== false) {
                throw new DuplicateSubscriberEmailException($message);
            } else {
                throw new PDOException($message);
            }
        }
    }

    public function update($subscriber_id, $network_user_name, $network_user_id, $network, $full_name,
    $oauth_access_token, $oauth_access_token_secret, $is_verified=0, $follower_count=0) {
        $q = " UPDATE subscribers SET network_user_name=:network_user_name, network_user_id = :network_user_id, ";
        $q .= "network = :network, full_name = :full_name, oauth_access_token = :oauth_access_token, ";
        $q .= "oauth_access_token_secret = :oauth_access_token_secret, is_verified = :is_verified, ";
        $q .= "follower_count = :follower_count WHERE id=:id";
        $vars = array(
            ':id'=>$subscriber_id,
            ':network_user_name'=>$network_user_name,
            ':network_user_id'=>$network_user_id,
            ':network'=>$network,
            ':full_name'=>$full_name,
            ':oauth_access_token'=>$oauth_access_token,
            ':oauth_access_token_secret'=>$oauth_access_token_secret,
            ':follower_count'=>$follower_count,
            ':is_verified'=>(integer) $is_verified
        );
        try {
            $ps = $this->execute($q, $vars);
            return $this->getUpdateCount($ps);
        } catch (PDOException $e) {
            $message = $e->getMessage();
            if (strpos($message,'Duplicate entry') !== false && strpos($message,'network_user_id') !== false) {
                throw new DuplicateSubscriberConnectionException($message);
            }
        }
    }

    //@TODO Cache these totals in their own table to avoid all these joins on the front page
    public function getTotalSubscribers($amount = 0) {
        $q = "SELECT count(*) as total FROM subscribers s ";
        if ($amount > 0) {
            $q .= "INNER JOIN subscriber_authorizations sa ON s.id = sa.subscriber_id ";
            $q .= "INNER JOIN authorizations a ON a.id = sa.authorization_id WHERE a.amount = :amount";
            $vars = array ( ':amount' => $amount);
            $ps = $this->execute($q, $vars);
        } else {
            $ps = $this->execute($q);
        }
        $result = $this->getDataRowAsArray($ps);
        return $result["total"];
    }

    public function getVerificationCode($email) {
        $q = "SELECT verification_code FROM subscribers WHERE email = :email;";
        $vars = array (':email'=>$email);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsArray($ps);
    }

    public function verifyEmailAddress($email) {
        $q = "UPDATE subscribers SET is_email_verified = 1 WHERE email = :email ";
        $vars = array (':email'=>$email);
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }
    /**
     * Generate a unique, random salt by appending the users email to a random number and returning the hash of it
     * @param str $email
     * @return str Salt
     */
    private function generateSalt($email){
        return hash('sha256', rand().$email);
    }
    /**
     * Hashes a password with a given salt.
     * @param str $password
     * @param str $salt
     * @param str Hashed password
     */
    private function hashPassword($password, $salt) {
        return hash('sha256', $password.$salt);
    }

    public function getByEmail($email) {
        $q = "SELECT * FROM subscribers WHERE email = :email";
        $vars = array ( ':email' => $email);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsObject($ps, "Subscriber");
    }

    public function getByID($subscriber_id) {
        $q = "SELECT * FROM subscribers WHERE id = :subscriber_id";
        $vars = array ( ':subscriber_id' => $subscriber_id);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsObject($ps, "Subscriber");
    }


    public function getSubscriberList($page_number=1, $count=50) {
        $start_on_record = ($page_number - 1) * $count;
        $q  = "SELECT * FROM subscribers s ";
        $q .= "INNER JOIN subscriber_authorizations sa ON s.id = sa.subscriber_id ";
        $q .= "INNER JOIN authorizations a ON sa.authorization_id = sa.authorization_id ";
        $q .= "INNER JOIN authorization_status_codes sc ON sc.code = a.status_code ";
        $q .= "ORDER BY s.creation_time DESC ";
        $q .= "LIMIT :start_on_record, :limit;";

        $vars = array(
            ':start_on_record'=>$start_on_record,
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, 'Subscriber');
    }

    public function getListTotal() {
        $q  = "SELECT count(*) as total FROM subscribers;";
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }
}