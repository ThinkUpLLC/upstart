<?php
class SubscriberMySQLDAO extends PDODAO {

    public function insertCompleteSubscriber(Subscriber $subscriber) {
        $verification_code = rand(1000, 9999);
        $pwd_salt = $this->generateSalt($subscriber->email);
        $hashed_pwd = $this->hashPassword($subscriber->pwd, $pwd_salt);

        $q  = "INSERT INTO subscribers (email, pwd, pwd_salt, creation_time, verification_code, network_user_id, ";
        $q .= "network_user_name, network, full_name, follower_count, is_verified, oauth_access_token, ";
        $q .= "oauth_access_token_secret, membership_level, thinkup_username, timezone ) VALUES ";
        $q .= "(:email, :pwd, :pwd_salt, CURRENT_TIMESTAMP, :verification_code, :network_user_id, ";
        $q .= ":network_user_name, :network, :full_name, :follower_count, :is_verified, :oauth_access_token, ";
        $q .= ":oauth_access_token_secret, :membership_level, :thinkup_username, :timezone); ";
        $vars = array(
            ':email'=>$subscriber->email,
            ':pwd'=>$hashed_pwd,
            ':pwd_salt'=>$pwd_salt,
            ':verification_code'=>$verification_code,
            ':network_user_id'=>$subscriber->network_user_id,
            ':network_user_name'=>$subscriber->network_user_name,
            ':network'=>$subscriber->network,
            ':full_name'=>$subscriber->full_name,
            ':follower_count'=>$subscriber->follower_count,
            ':is_verified'=>$subscriber->is_verified,
            ':oauth_access_token'=>$subscriber->oauth_access_token,
            ':oauth_access_token_secret'=>$subscriber->oauth_access_token_secret,
            ':membership_level'=>$subscriber->membership_level,
            ':thinkup_username'=>$subscriber->thinkup_username,
            ':timezone'=>$subscriber->timezone
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        try {
            $ps = $this->execute($q, $vars);
            return $this->getInsertId($ps);
        } catch (PDOException $e) {
            $message = $e->getMessage();
            if (strpos($message,'Duplicate entry') !== false && strpos($message, "for key 'email'") !== false) {
                throw new DuplicateSubscriberEmailException($message);
            } elseif (strpos($message,'Duplicate entry') !== false
                && strpos($message,"for key 'thinkup_username'") !== false) {
                throw new DuplicateSubscriberUsernameException($message);
            } elseif (strpos($message,'Duplicate entry') !== false
                && strpos($message,"for key 'network_user_id'") !== false) {
                throw new DuplicateSubscriberConnectionException($message);
            } else {
                throw new PDOException($message);
            }
        }

    }

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
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
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
            if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
            $ps = $this->execute($q, $vars);
            return $this->getUpdateCount($ps);
        } catch (PDOException $e) {
            $message = $e->getMessage();
            if (strpos($message,'Duplicate entry') !== false
                && strpos($message,"for key 'network_user_id'") !== false) {
                throw new DuplicateSubscriberConnectionException($message);
            }
        }
    }

    public function setUsername($subscriber_id, $username) {
        $q = "UPDATE subscribers SET thinkup_username = :thinkup_username WHERE id=:id";
        $vars = array(
            ':id'=>$subscriber_id,
            ':thinkup_username'=>$username
        );
        try {
            if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
            $ps = $this->execute($q, $vars);
            if ($this->getUpdateCount($ps) == 0) {
                return false;
            } else {
                return true;
            }
        } catch (PDOException $e) {
            $message = $e->getMessage();
            if (strpos($message,'Duplicate entry') !== false && strpos($message,'thinkup_username') !== false) {
                throw new DuplicateSubscriberUsernameException($message);
            }
        }
    }

    public function setSubscriptionStatus($subscriber_id, $subscription_status) {
        $q = "UPDATE subscribers SET subscription_status = :subscription_status WHERE id=:id";
        $vars = array(
            ':id'=>$subscriber_id,
            ':subscription_status'=>$subscription_status
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function setPaidThrough($subscriber_id, $paid_through) {
        $q = "UPDATE subscribers SET paid_through = :paid_through WHERE id=:id";
        $vars = array(
            ':id'=>$subscriber_id,
            ':paid_through'=>$paid_through
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function setLastCrawl($thinkup_username) {
        $q = <<<EOD
UPDATE subscribers SET last_crawl_completed = NOW(), is_crawl_in_progress = 0
WHERE thinkup_username = :thinkup_username
EOD;
        $vars = array(
            ':thinkup_username'=>$thinkup_username
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function setSubscriptionRecurrence($subscriber_id, $subscription_recurrence) {
        $q = "UPDATE subscribers SET subscription_recurrence = :subscription_recurrence WHERE id=:id";
        $vars = array(
            ':id'=>$subscriber_id,
            ':subscription_recurrence'=>$subscription_recurrence
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function setSubscriptionDetails($subscriber) {
        $q = "UPDATE subscribers SET subscription_status = :subscription_status, paid_through = :paid_through, ";
        $q .= "subscription_recurrence = :subscription_recurrence, is_via_recurly = :is_via_recurly, ";
        $q .= "recurly_subscription_id = :recurly_subscription_id WHERE id=:id";
        $vars = array(
            ':id'=>$subscriber->id,
            ':subscription_status'=>$subscriber->subscription_status,
            ':paid_through'=>$subscriber->paid_through,
            ':subscription_recurrence'=>$subscriber->subscription_recurrence,
            ':is_via_recurly' =>  $this->convertBoolToDB($subscriber->is_via_recurly),
            ':recurly_subscription_id' => $subscriber->recurly_subscription_id
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    /**
     * Apply a claim code to a subscriber: set subscription_recurrence to None, paid_through date, and claim_code field.
     * @param  int $subscriber_id
     * @param  ClaimCode $claim_code
     * @return int update count
     */
    public function redeemClaimCode($subscriber_id, ClaimCode $claim_code) {
        $paid_through = date('Y-m-d H:i:s', strtotime('+'.$claim_code->number_days.'days'));
        $q = "UPDATE subscribers SET subscription_recurrence = 'None', paid_through = :paid_through, ";
        $q .= "claim_code = :claim_code, subscription_status = 'Paid' WHERE id=:id";
        $vars = array(
            ':id'=>$subscriber_id,
            ':paid_through'=>$paid_through,
            ':claim_code'=>$claim_code->code
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function setEmail($subscriber_id, $email) {
        $q = " UPDATE subscribers SET email = :email WHERE id=:id";
        $vars = array(
            ':id'=>$subscriber_id,
            ':email'=>$email
        );
        try {
            if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
            $ps = $this->execute($q, $vars);
            if ($this->getUpdateCount($ps) == 0) {
                return false;
            } else {
                return true;
            }
        } catch (PDOException $e) {
            $message = $e->getMessage();
            if (strpos($message,'Duplicate entry') !== false && strpos($message,'email') !== false) {
                throw new DuplicateSubscriberEmailException($message);
            }
        }
    }

    public function setRecurlySubscriptionID($subscriber_id, $recurly_subscription_id) {
        $q = " UPDATE subscribers SET recurly_subscription_id = :recurly_subscription_id WHERE id=:id";
        $vars = array(
            ':id'=>$subscriber_id,
            ':recurly_subscription_id'=>$recurly_subscription_id
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        if ($this->getUpdateCount($ps) == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getVerificationCode($email) {
        $q = "SELECT verification_code FROM subscribers WHERE email = :email;";
        $vars = array (':email'=>$email);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsArray($ps);
    }

    public function verifyEmailAddress($email) {
        $q = "UPDATE subscribers SET is_email_verified = 1 WHERE email = :email ";
        $vars = array (':email'=>$email);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
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
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        $subscriber =  $this->getDataRowAsObject($ps, "Subscriber");
        if (!isset($subscriber)) {
            throw new SubscriberDoesNotExistException('Subscriber '.$email.' does not exist.');
        }
        return $subscriber;
    }

    public function getByID($subscriber_id) {
        $q = "SELECT * FROM subscribers WHERE id = :subscriber_id";
        $vars = array ( ':subscriber_id' => $subscriber_id);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        $subscriber = $this->getDataRowAsObject($ps, "Subscriber");
        if (!isset($subscriber)) {
            throw new SubscriberDoesNotExistException('Subscriber ID '.$subscriber_id.' does not exist.');
        }
        return $subscriber;
    }

    public function getSubscriberList($page_number=1, $count=50) {
        $start_on_record = ($page_number - 1) * $count;
        $q  = "SELECT * FROM subscribers s ";
        $q .= "ORDER BY s.creation_time DESC ";
        $q .= "LIMIT :start_on_record, :limit;";

        $vars = array(
            ':start_on_record'=>$start_on_record,
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, 'Subscriber');
    }

    public function getSubscriberListWithPaymentStatus($status, $page_number=1, $count=50) {
        $start_on_record = ($page_number - 1) * $count;
        $q  = "SELECT * FROM subscribers s WHERE subscription_status = :status ";
        $q .= "ORDER BY s.creation_time DESC ";
        $q .= "LIMIT :start_on_record, :limit;";

        $vars = array(
            ':status'=>$status,
            ':start_on_record'=>$start_on_record,
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, 'Subscriber');
    }

    public function getPaidTotal() {
        $q  = "SELECT id, subscription_recurrence, is_via_recurly FROM subscribers s ";
        $q .= "WHERE subscription_status = 'Paid' AND is_account_closed != 1 ";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        $rows = $this->getDataRowsAsArrays($ps);
        $total_paid_subscribers = 0;
        $breakdown = array('monthly'=>0, 'annual'=>0, 'recurly'=>0, 'coupon_codes'=>0);
        foreach ($rows as $row) {
            $total_paid_subscribers++;
            if ($row['subscription_recurrence'] == '1 month') {
                $breakdown['monthly']++;
            } else if ($row['subscription_recurrence'] == '12 months') {
                $breakdown['annual']++;
            } else if ($row['subscription_recurrence'] == 'None') {
                $breakdown['coupon_codes']++;
            }
            if ($row['is_via_recurly']) {
                $breakdown['recurly']++;
            }
        }
        return array('total_paid_subscribers'=>$total_paid_subscribers, 'breakdown'=>$breakdown);
    }

    public function getSearchResults($search_term, $page_number = 1, $count = 50) {
        $start_on_record = ($page_number - 1) * $count;
        $q = "SELECT * FROM subscribers s ";
        $q .= "WHERE email LIKE :search_term OR network_user_name LIKE :search_term1 OR full_name LIKE :search_term2 ";
        $q .= "OR thinkup_username LIKE :search_term ";
        $q .= "ORDER BY s.creation_time DESC ";
        $q .= "LIMIT :start_on_record, :limit;";

        $vars = array(
            ':search_term1'=>'%'.$search_term.'%',
            ':search_term2'=>'%'.$search_term.'%',
            ':search_term'=>'%'.$search_term.'%',
            ':start_on_record'=>$start_on_record,
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, 'Subscriber');
    }

    public function archiveSubscriber($subscriber_id) {
        $q = "INSERT INTO subscriber_archive SELECT s.id, s.email, s.pwd, s.pwd_salt, s.creation_time, ";
        $q .= "s.network_user_id, s.network_user_name, s.network, s.full_name, s.follower_count, s.is_verified, ";
        $q .= "s.oauth_access_token_secret, s.verification_code, s.is_email_verified, s.is_from_waitlist, ";
        $q .= "s.oauth_access_token, s.membership_level, s.thinkup_username, s.date_installed, s.api_key_private, ";
        $q .= "s.last_dispatched, s.commit_hash, s.is_installation_active, a.token_id, a.amount, ";
        $q .= "a.status_code, a.error_message, a.payment_method_expiry, a.caller_reference, a.recurrence_period, ";
        $q .= "a.token_validity_start_date, s.subscription_status, s.paid_through, s.total_payment_reminders_sent, ";
        $q .= "s.payment_reminder_last_sent, s.is_account_closed, s.claim_code ";
        $q .= "FROM subscribers s LEFT JOIN subscriber_authorizations sa ";
        $q .= "ON s.id = sa.subscriber_id LEFT JOIN authorizations a ON a.id = sa.authorization_id ";
        $q .= "WHERE s.id = :subscriber_id";

        $vars = array(
            ':subscriber_id'=>$subscriber_id
        );
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function deleteBySubscriberID($subscriber_id) {
        $q  = "DELETE FROM subscribers WHERE id = :subscriber_id";
        $vars = array(
            ':subscriber_id'=>$subscriber_id
        );
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function intializeInstallation($id, $api_key_private, $commit_hash) {
        $q  = "UPDATE subscribers SET date_installed=NOW(), api_key_private = :api_key_private, ";
        $q .= "commit_hash = :commit_hash, is_installation_active=1 WHERE id = :id ";
        $vars = array(
            ':id'=>$id,
            ':api_key_private'=>$api_key_private,
            ':commit_hash'=>$commit_hash,
        );
        $ps = $this->execute($q, $vars);
    }

    public function updateDateInstalled($id, $date_installed) {
        $q  = "UPDATE subscribers SET date_installed = :date_installed ";
        $q .= "WHERE id = :id ";

        $vars = array(
            ':id'=>(int) $id,
            ':date_installed'=>$date_installed
        );
        //echo self::mergeSQLVars($q, $vars)."\n";
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function updateLastDispatchedTime($id) {
        $q  = "UPDATE subscribers SET last_dispatched = CURRENT_TIMESTAMP(), is_crawl_in_progress = 1 ";
        $q .= "WHERE id = :id ";

        $vars = array(
            ':id'=>(int) $id
        );
        //echo self::mergeSQLVars($q, $vars)."\n";
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function resetLastDispatchedTime($id) {
        $q  = "UPDATE subscribers SET last_dispatched = null, is_crawl_in_progress = 0 ";
        $q .= "WHERE id = :id ";

        $vars = array(
            ':id'=>(int) $id
        );
        //echo self::mergeSQLVars($q, $vars)."\n";
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function updateCommitHash($id, $commit_hash) {
        $q  = "UPDATE subscribers SET commit_hash = :commit_hash ";
        $q .= "WHERE id = :id ";

        $vars = array(
            ':commit_hash'=>$commit_hash,
            ':id'=>(int) $id
        );
        //echo self::mergeSQLVars($q, $vars)."\n";
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function doesSubscriberConnectionExist($network_user_id, $network) {
        $q  = "SELECT id FROM subscribers WHERE network_user_id = :network_user_id AND network = :network ";
        $vars = array(
            ':network_user_id'=>$network_user_id,
            ':network'=>$network
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        $returned_subscribers = $this->getDataRowsAsArrays($ps);
        return (count($returned_subscribers) > 0);
    }

    public function doesSubscriberEmailExist($email) {
        $q  = "SELECT id FROM subscribers WHERE email = :email ";
        $vars = array(
            ':email'=>$email
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        $returned_subscribers = $this->getDataRowsAsArrays($ps);
        return (count($returned_subscribers) > 0);
    }

    public function getTotalInstallsToUpgrade($commit_hash) {
        $q  = "SELECT count(*) as total FROM subscribers WHERE is_installation_active = 1 ";
        $q .= "AND commit_hash != :commit_hash;";

        $vars = array(
            ':commit_hash'=>$commit_hash
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }

    public function getInstallsToUpgrade($commit_hash) {
        $q  = "SELECT * FROM subscribers WHERE is_installation_active = 1 ";
        $q .= "AND commit_hash != :commit_hash LIMIT 10;";

        $vars = array(
            ':commit_hash'=>$commit_hash
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    public function setInstallationActive($id, $is_installation_active) {
        $q  = "UPDATE subscribers SET is_installation_active = :is_installation_active ";
        $q .= "WHERE id = :id ";

        $vars = array(
            ':is_installation_active'=> (int) $is_installation_active,
            ':id'=>(int) $id
        );
        //echo self::mergeSQLVars($q, $vars)."\n";
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function closeAccount($id) {
        return self::setIsAccountClosed($id, 1);
    }

    public function openAccount($id) {
        return self::setIsAccountClosed($id, 0);
    }

    private function setIsAccountClosed($id, $is_account_closed) {
        $q  = "UPDATE subscribers SET is_account_closed = :is_account_closed ";
        $q .= "WHERE id = :id ";

        $vars = array(
            ':is_account_closed'=> (int) $is_account_closed,
            ':id'=>(int) $id
        );
        //echo self::mergeSQLVars($q, $vars)."\n";
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function setMembershipLevel($id, $membership_level) {
        $q  = "UPDATE subscribers SET membership_level = :membership_level ";
        $q .= "WHERE id = :id ";

        $vars = array(
            ':membership_level'=> $membership_level,
            ':id'=>(int) $id
        );
        //echo self::mergeSQLVars($q, $vars)."\n";
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function getSubscribersInstalled($count=25) {
        $q  = "SELECT id FROM subscribers WHERE date_installed IS NOT NULL AND is_installation_active = 1 ";
        $q .= "LIMIT :limit;";

        $vars = array(
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    /**
     * Get installations to crawl for members who have paid or are complimentary.
     * @param int $hours_stale How many hours stale is the install
     * @param int $count How many installs to retrieve; defaults to 25
     * @return arr Array of installation information
     */
    public function getPaidStaleInstalls($hours_stale, $count=25) {
        $q  = <<<EOD
SELECT * FROM subscribers WHERE is_installation_active = 1 AND is_account_closed = 0
AND (subscription_status = 'Paid' OR is_membership_complimentary = 1)
AND is_crawl_in_progress = 0
AND (last_crawl_completed IS NULL OR last_crawl_completed < DATE_SUB(NOW(), INTERVAL :hours_stale HOUR))
ORDER BY last_crawl_completed ASC
LIMIT :limit;
EOD;

        $vars = array(
            ':limit'=>$count,
            ':hours_stale'=>$hours_stale
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    /**
     * Get installations to crawl for members who have not paid.
     * This function does not return members who have closed their account.
     * This function does not return members who have received 4 payment reminders, last one within last 2 days.
     * @param int $hours_stale How many hours stale is the install
     * @param int $count How many installs to retrieve; defaults to 25
     * @return arr Array of installation information
     */
    public function getNotYetPaidStaleInstalls($hours_stale, $count=25) {
        $not_yet_paid_criteria = self::getNotYetPaidCriteria();
        $q  = <<<EOD
SELECT * FROM subscribers WHERE is_installation_active = 1 AND is_account_closed = 0
AND is_crawl_in_progress = 0
AND (last_crawl_completed IS NULL OR last_crawl_completed < DATE_SUB(NOW(), INTERVAL :hours_stale HOUR))
$not_yet_paid_criteria
ORDER BY last_crawl_completed ASC
LIMIT :limit;
EOD;

        $vars = array(
            ':limit'=>$count,
            ':hours_stale'=>$hours_stale
        );
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    public function getPaidStalestInstallLastCrawlCompletedTime() {
        $q  = "SELECT last_crawl_completed FROM subscribers WHERE is_installation_active=1 AND is_account_closed != 1 ";
        $q .= "AND subscription_status = 'Paid' ";
        $q .= "ORDER BY last_crawl_completed ASC LIMIT 1";
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['last_crawl_completed'];
    }

    public function getNotPaidStalestInstallLastCrawlCompletedTime() {
        $q  = "SELECT last_crawl_completed FROM subscribers WHERE is_installation_active=1 AND is_account_closed != 1 ";
        $q .= self::getNotYetPaidCriteria();
        $q .= "ORDER BY last_crawl_completed ASC LIMIT 1";
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['last_crawl_completed'];
    }

    public function getNotPaidStalestInstallLastDispatchTime() {
        $q  = "SELECT last_dispatched FROM subscribers WHERE is_installation_active=1 AND is_account_closed != 1 ";
        $q .= self::getNotYetPaidCriteria();
        $q .= "ORDER BY last_dispatched ASC LIMIT 1";
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['last_dispatched'];
    }

   public function getPaidStalestInstallLastDispatchTime() {
        $q  = "SELECT last_dispatched FROM subscribers WHERE is_installation_active=1 AND is_account_closed != 1 ";
        $q .= "AND subscription_status = 'Paid' ";
        $q .= "ORDER BY last_dispatched ASC LIMIT 1";
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['last_dispatched'];
    }

    private function getNotYetPaidCriteria() {
        // Note: this returns Payment failed, Payment due, and Complimentary memberships as well as Free trial
        $q = "AND subscription_status != 'Paid' ";
        // Upstart hasn't sent all payment reminders
        $q .= "AND (total_payment_reminders_sent < 4  OR ";
        // Upstart's sent all the payment reminders but the last one was sent within the last 2 days
        $q .= "(total_payment_reminders_sent = 4 AND payment_reminder_last_sent > DATE_SUB(NOW(), INTERVAL 2 DAY ))) ";
        return $q;
    }

    public function getTotalActiveInstalls() {
        $q  = "SELECT count(*) AS total FROM subscribers WHERE is_installation_active = 1 and is_account_closed = 0;";
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }

    public function isAuthorized($email, $password) {
        // Get salt from the database
        $db_salt = $this->getSaltByEmail($email);
        // Get password from the database
        $db_password = $this->getPass($email);

        $hashed_pwd = $this->hashPassword($password, $db_salt); // Hash the new way
        // Check if it matches the password stored in the database
        return ($hashed_pwd == $db_password);
    }

    /**
     * Retrives the salt for a given user
     * @param str $email
     * @return str Salt
     */
    private function getSaltByEmail($email){
        $q = "SELECT pwd_salt ";
        $q .= "FROM subscribers s ";
        $q .= "WHERE s.email = :email";
        $vars = array(':email'=>$email);
        $ps = $this->execute($q, $vars);
        $query = $this->getDataRowAsArray($ps);
        return $query['pwd_salt'];
    }

    public function getPass($email) {
        $q = "SELECT pwd FROM subscribers WHERE email = :email LIMIT 1;";
        $vars = array(
            ':email'=>$email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        $result = $this->getDataRowAsArray($ps);
        if (isset($result['pwd'])) {
            return $result['pwd'];
        } else {
            return false;
        }
    }

    public function updateLastLogin($email) {
        $q = " UPDATE subscribers SET last_login=now() WHERE email=:email";
        $vars = array(
            ':email'=>$email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function resetFailedLogins($email) {
        $q = "UPDATE subscribers
              SET failed_logins=0
              WHERE email=:email";
        $vars = array(
            ":email" => $email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return ( $this->getUpdateCount($ps) > 0 )? true : false;
    }

    public function setAccountStatus($email, $status) {
        $q = "UPDATE subscribers
              SET account_status=:account_status
              WHERE email=:email";
        $vars = array(
            ":account_status" => $status,
            ":email" => $email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return ( $this->getUpdateCount($ps) > 0 )? true : false;
    }

    public function clearAccountStatus($email) {
        return  $this->setAccountStatus($email, '');
    }

    public function incrementFailedLogins($email) {
        $q = "UPDATE subscribers
              SET failed_logins=failed_logins+1
              WHERE email=:email";
        $vars = array(
            ":email" => $email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return ( $this->getUpdateCount($ps) > 0 )? true : false;
    }

    public function deactivateSubscriber($email) {
        $this->updateActivation($email, false);
    }

    public function activateSubscriber($email) {
        $this->updateActivation($email, true);
    }

    /**
     * Set the value of the is_activated field.
     * @param str $email
     * @param bool $is_activated
     * @return int Count of affected rows
     */
    private function updateActivation($email, $is_activated) {
        $q = " UPDATE subscribers SET is_activated=:is_activated WHERE email=:email";
        $vars = array(
            ':email'=>$email,
            ':is_activated'=>(($is_activated)?1:0)
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function updatePasswordToken($email, $token) {
        $q = "UPDATE subscribers
              SET password_token=:token
              WHERE email=:email";
        $vars = array(
            ":token" => $token,
            ":email" => $email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function updateSubscriptionRecurrence($id, $subscription_recurrence) {
        $q = "UPDATE subscribers
              SET subscription_recurrence=:subscription_recurrence
              WHERE id=:id";
        $vars = array(
            ":id" => $id,
            ":subscription_recurrence" => $subscription_recurrence
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function getByPasswordToken($token) {
        $q = "SELECT * FROM subscribers WHERE password_token LIKE :token";
        $vars = array(':token' => $token . '_%');
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsObject($ps, 'Subscriber');
    }

    public function updatePassword($email, $pwd) {
        // Generate new unique salt and store it in the database
        $salt = $this->generateSalt($email);
        $this->updateSalt($email, $salt);
        //Hash the password using the new salt
        $hashed_password = $this->hashPassword($pwd, $salt);
        //Store the new hashed password in the database
        $q = " UPDATE subscribers SET pwd=:hashed_password WHERE email=:email";
        $vars = array(
            ':email'=>$email,
            ':hashed_password'=>$hashed_password
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    /**
     * Updates the password salt for a given user
     * @param str $email
     * @param str $salt
     * @return int Number of rows updated
     */
    private function updateSalt($email, $salt) {
        $q = "UPDATE subscribers SET pwd_salt=:salt WHERE email=:email";
        $vars = array(
            ':email'=>$email,
            ':salt'=>$salt
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    /**
     * Check if a ThinkUp username is in use.
     * @param  str  $thinkup_username Username to check
     * @return bool  Whether or not it is in use
     */
    public function isUsernameTaken($thinkup_username) {
        $reserved_names = array('www', 'stage', 'demo', 'book', 'shares', 'images');
        if (in_array($thinkup_username, $reserved_names) ) {
            return true;
        }
        $q = "SELECT thinkup_username FROM subscribers WHERE thinkup_username=:thinkup_username";
        $vars = array(
            //The username is always lowercase
            ':thinkup_username'=>strtolower($thinkup_username)
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataIsReturned($ps);
    }

    /**
     * Get annual subscribers who have a payment due.
     * @param  int $count Default to 10
     * @return arr
     */
    public function getAnnualSubscribersToCharge($count=10) {
        $q = "SELECT s.id, s.email, s.membership_level, a.token_id FROM subscribers s
            INNER JOIN subscriber_authorizations sa ON sa.subscriber_id = s.id
            INNER JOIN authorizations a ON sa.authorization_id = a.id
            WHERE s.subscription_recurrence = '12 months' AND paid_through <= :due_date
            AND s.is_membership_complimentary = 0 AND is_account_closed != 1 LIMIT :count ";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $vars = array(
            ':due_date'=>date('Y-m-d H:i:s', strtotime('-1 hour')),
            ':count'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    /**
     * Get total annual subscribers who have a payment due.
     * @return int
     */
    public function getTotalAnnualSubscribersToCharge($count=10) {
        $q = "SELECT count(s.id) as total FROM subscribers s
            INNER JOIN subscriber_authorizations sa ON sa.subscriber_id = s.id
            INNER JOIN authorizations a ON sa.authorization_id = a.id
            WHERE s.subscription_recurrence = '12 months' AND paid_through <= :due_date
            AND s.is_membership_complimentary = 0 AND is_account_closed != 1";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $vars = array(
            ':due_date'=>date('Y-m-d H:i:s', strtotime('-1 hour'))
        );
        $ps = $this->execute($q, $vars);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }

    /**
     * Update Subscriber->paid_through and Subscriber->subscription_status in data store.
     * @param  Subscriber $subscriber
     * @return void
     */
    public function updateSubscriberSubscriptionDetails(Subscriber $subscriber) {
        $subscription_helper = new SubscriptionHelper();
        $new_values = $subscription_helper->getSubscriptionStatusAndPaidThrough( $subscriber );
        $this->setSubscriptionStatus($subscriber->id, $new_values['subscription_status']);
        $this->setPaidThrough($subscriber->id, $new_values['paid_through']);
    }

    public function compSubscription($subscriber_id) {
        return $this->setIsComplimentary($subscriber_id, true);
    }

    public function decompSubscription($subscriber_id) {
        return $this->setIsComplimentary($subscriber_id, false);
    }

    private function setIsComplimentary($subscriber_id, $is_complimentary) {
        $q = "UPDATE subscribers SET is_membership_complimentary = :is_membership_complimentary ";
        $q .="WHERE id = :subscriber_id;";
        $vars = array(
            ':subscriber_id'=>$subscriber_id,
            ':is_membership_complimentary'=>$is_complimentary
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    /**
     * Get 25 free trial subscribers who are due a payment reminder email.
     * @param  int $total_reminders_sent How many reminders already sent to these subscribers
     * @param  int $hours_past_time      How many hours past signup or last reminder time.
     * @return arr                       Array of Subscriber objects
     */
    public function getSubscribersFreeTrialPaymentReminder($total_reminders_sent, $hours_past_time) {
        $q = "SELECT * FROM subscribers WHERE subscription_status = 'Free Trial' ";
        $q .= "AND is_account_closed = 0 ";
        $q .= "AND total_payment_reminders_sent = :total_reminders_sent AND (";
        //If total_reminders_sent = 0, use creation_time to compare. Otherwise, use payment_reminder_last_sent.
        if ($total_reminders_sent == 0) {
            $q .= "creation_time ";
        } else {
            $q .= "payment_reminder_last_sent ";
        }
        $q .= "< DATE_SUB(NOW(), INTERVAL :hours_past_time HOUR )) ORDER BY creation_time ASC LIMIT 25";
        $vars = array(':total_reminders_sent' => $total_reminders_sent, ':hours_past_time' => $hours_past_time);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, 'Subscriber');
    }

    /**
     * Get 25 annual subscribers who have a payment due in the next X days. If X is a negative number, then payment
     * was due X days ago.
     * @param int $days_before Number of days before paid through date to send reminder
     * @param int $total_reup_reminders_sent Only send to members who have less than this total number of reminders
     * @return arr Array of Subscriber objects
     */
    public function getAnnualSubscribersDueReupReminder($days_before, $total_reup_reminders_sent) {
        $q = <<<EOD
        SELECT * FROM subscribers WHERE subscription_status = 'Paid' AND is_account_closed = 0
        AND is_via_recurly = 0
        AND subscription_recurrence = '12 months'
        AND date(paid_through) = DATE(DATE_ADD(NOW(), INTERVAL :days_before DAY ))
        AND total_reup_reminders_sent < :total_reup_reminders_sent
        ORDER BY creation_time ASC LIMIT 25
EOD;

        $vars = array(':days_before' => $days_before, ':total_reup_reminders_sent' => $total_reup_reminders_sent);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, 'Subscriber');
    }

    /**
     * Get 25 subscribers to uninstall because free trial has expired and it's been 30 hours since last dispatch time.
     * @return arr Array of Subscriber objects
     */
    public function getSubscribersToUninstallDueToExpiredTrial() {
        $q = "SELECT * FROM subscribers WHERE subscription_status = 'Free trial' ";
        //AND trial is more than 15 days old, and last dispatched is over 30 hours ago.
        $q .= "AND (creation_time < DATE_SUB(NOW(), INTERVAL 15 DAY )) ";
        $q .= "AND (last_crawl_completed < DATE_SUB(NOW(), INTERVAL 30 HOUR )) ";
        $q .= "ORDER BY creation_time ASC LIMIT 25";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        return $this->getDataRowsAsObjects($ps, 'Subscriber');
    }

    /**
     * Get 25 subscribers to uninstall because the account is closed and it's been 30 hours since last dispatch time.
     * @return arr Array of Subscriber objects
     */
    public function getSubscribersToUninstallDueToAccountClosure() {
        $q = "SELECT * FROM subscribers WHERE is_account_closed = 1 ";
        $q .= "AND (last_crawl_completed < DATE_SUB(NOW(), INTERVAL 30 HOUR )) ";
        $q .= "ORDER BY creation_time ASC LIMIT 25";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        return $this->getDataRowsAsObjects($ps, 'Subscriber');
    }

    public function setTotalPaymentRemindersSent($subscriber_id, $total_payment_reminders_sent) {
        $q = "UPDATE subscribers SET total_payment_reminders_sent = :total_payment_reminders_sent, ";
        $q .="payment_reminder_last_sent =  NOW() WHERE id = :subscriber_id;";
        $vars = array(
            ':subscriber_id'=>$subscriber_id,
            ':total_payment_reminders_sent'=>$total_payment_reminders_sent
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function setTotalReupRemindersSent($subscriber_id, $total_reup_reminders_sent) {
        $q = "UPDATE subscribers SET total_reup_reminders_sent = :total_reup_reminders_sent, ";
        $q .="reup_reminder_last_sent =  NOW() WHERE id = :subscriber_id;";
        $vars = array(
            ':subscriber_id'=>$subscriber_id,
            ':total_reup_reminders_sent'=>$total_reup_reminders_sent
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    /**
     * Get last X days worth of member signups.
     * @param int $limit how many days
     * @return array
     */
    public function getDailySignups($limit = 365) {
        $q = "SELECT SUM(total_subscribers) AS total_subscribers, signup_date FROM
(
SELECT COUNT(id) as total_subscribers, DATE(creation_time) as signup_date
FROM subscribers WHERE creation_time >= DATE_SUB(NOW(), INTERVAL :limit DAY)
GROUP BY DATE(subscribers.creation_time)
UNION
SELECT COUNT(id) as total_subscribers, DATE(creation_time) as signup_date
FROM subscriber_archive WHERE creation_time >= DATE_SUB(NOW(), INTERVAL :limit DAY)
GROUP BY DATE(subscriber_archive.creation_time)
) AS signups
GROUP BY signup_date ORDER BY signup_date DESC LIMIT 0, :limit;";
        $vars = array(':limit'=>$limit);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        $rows = $this->getDataRowsAsArrays($ps);
        $results = array();
        foreach ($rows as $row) {
            $results[$row['signup_date']] = $row['total_subscribers'];
        }
        ksort($results);
        return $results;
    }

    public function getWeeklySignups($limit = 52) {
        $q = "SELECT DATE(creation_time) AS signup_week, COUNT(email) AS total_signups
FROM
( SELECT email, creation_time, thinkup_username FROM subscriber_archive WHERE creation_time > '2014-07-08'
UNION
SELECT email, creation_time, thinkup_username FROM subscribers WHERE creation_time > '2014-07-08') all_subscribers
GROUP BY WEEKOFYEAR(creation_time), YEAR(creation_time) ORDER BY creation_time ASC
";
        $vars = array(':limit'=>$limit);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        $rows = $this->getDataRowsAsArrays($ps);
        $results = array();
        foreach ($rows as $row) {
            $results[$row['signup_week']] = $row['total_signups'];
        }
        ksort($results);
        return $results;
    }

    /**
     * Get paid subscriber counts over time.
     * @return array
     */
    public function getDailyPaidSubscriberCounts($since = '2015-07-01') {
        $q = "SELECT date(date) as date, count
            FROM subscriber_paid_counts WHERE is_via_recurly = 0 AND date(date) >= :since
            ORDER BY date DESC;";
        $vars = array(':since'=>$since);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        $rows = $this->getDataRowsAsArrays($ps);
        $results = array();
        foreach ($rows as $row) {
            $results[$row['date']] = $row['count'];
        }
        ksort($results);
        return $results;
    }

    /**
     * Get paid subscriber counts on Recurly over time.
     * @return array
     */
    public function getDailyPaidRecurlySubscriberCounts($since = '2015-07-01') {
        $q = "SELECT date(date) as date, count
            FROM subscriber_paid_counts WHERE is_via_recurly = 1 AND date(date) >= :since
            ORDER BY date DESC;";
        $vars = array(':since'=>$since);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        $rows = $this->getDataRowsAsArrays($ps);
        $results = array();
        foreach ($rows as $row) {
            $results[$row['date']] = $row['count'];
        }
        ksort($results);
        return $results;
    }

    /**
     * Get last three days worth of member signups.
     * @TODO Delete this as of June 1 2015.
     * @return array
     */
    public function getReupsDueToday() {
        $q = "SELECT COUNT(id) as reups_due
            FROM subscribers WHERE DATE(paid_through) = DATE(NOW());";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        $rows = $this->getDataRowAsArray($ps);
        return $rows['reups_due'];
    }

    public function captureCurrentPaidCount() {
        //Total paid subscribers
        $q = "INSERT INTO subscriber_paid_counts (date, count, is_via_recurly)  ";
        $q .= "SELECT NOW(), count(*), 0 FROM subscribers s ";
        $q .= "WHERE subscription_status = 'Paid' AND is_account_closed != 1";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);

        //Paid via Recurly
        $q = "INSERT INTO subscriber_paid_counts (date, count, is_via_recurly)  ";
        $q .= "SELECT NOW(), count(*), 1 FROM subscribers s ";
        $q .= "WHERE subscription_status = 'Paid' AND is_account_closed != 1 AND is_via_recurly = 1";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
    }
}
