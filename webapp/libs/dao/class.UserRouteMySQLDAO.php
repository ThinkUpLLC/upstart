<?php
class UserRouteMySQLDAO extends PDODAO {

    public function insert($email, $twitter_username, $twitter_user_id, $oauth_access_token, $oauth_access_token_secret,
    $is_verified, $follower_count, $full_name) {
        $q  = "INSERT INTO user_routes (email, twitter_username, twitter_user_id, oauth_access_token, ";
        $q .= "oauth_access_token_secret, is_verified, follower_count, full_name) VALUES (:email, ";
        $q .= ":twitter_username, :twitter_user_id, :oauth_access_token, :oauth_access_token_secret, ";
        $q .= ":is_verified, :follower_count, :full_name); ";
        $vars = array(
            ':email'=>$email,
            ':twitter_username'=>$twitter_username,
            ':twitter_user_id'=>$twitter_user_id,
            ':oauth_access_token'=>$oauth_access_token,
            ':oauth_access_token_secret'=>$oauth_access_token_secret,
            ':is_verified'=>(integer) $is_verified,
            ':follower_count'=>(integer) $follower_count,
            ':full_name'=>$full_name
        );
        //echo self::mergeSQLVars($q, $vars);
        try {
            $ps = $this->execute($q, $vars);
        } catch (PDOException $e) {
            $exception = $e->getMessage();
            if ((preg_match('/Duplicate entry/', $exception)>0) && (preg_match('/for key \'email\'/', $exception)>0)) {
                throw new DuplicateUserRouteException();
            }
        }
        return $this->getInsertId($ps);
    }

    public function get($email, $twitter_user_id) {
        $q  = "SELECT id FROM user_routes ";
        $q .= "WHERE email = :email AND twitter_user_id = :twitter_user_id ";
        $vars = array(
            ':email'=>$email,
            ':twitter_user_id'=>$twitter_user_id
        );
        $ps = $this->execute($q, $vars);
        $result = $this->getDataRowAsArray($ps);
        return $result['id'];
    }

    public function getListTotal() {
        $q  = "SELECT count(*) as total FROM user_routes;";
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }

    public function getById($id) {
        $q  = "SELECT * FROM user_routes ";
        $q .= "WHERE id = :id ";
        $vars = array(
            ':id'=>$id
        );
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsArray($ps);
    }

    public function updateRoute($id, $route, $database_name, $commit_hash, $is_active=1) {
        $cfg = Config::getInstance();
        $db_name = $cfg->getValue('db_name');
        $q  = "USE ".$db_name."; UPDATE user_routes SET route = :route, database_name = :database_name, ";
        $q .= "commit_hash = :commit_hash, is_active = :is_active WHERE id = :id ";
        $vars = array(
            ':id'=>$id,
            ':route'=>$route,
            ':database_name'=>$database_name,
            ':commit_hash'=>$commit_hash,
            ':is_active'=>$is_active
        );
        $ps = $this->execute($q, $vars);
    }

    public function getUserList($page_number=1, $count=50) {
        $start_on_record = ($page_number - 1) * $count;
        $q  = "SELECT * FROM user_routes ";
        $q .= "ORDER BY date_waitlisted DESC ";
        $q .= "LIMIT :start_on_record, :limit;";

        $vars = array(
            ':start_on_record'=>$start_on_record,
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    public function getInstallsToUpgrade($commit_hash) {
        $q  = "SELECT * FROM user_routes WHERE is_active = 1 ";
        $q .= "AND commit_hash != :commit_hash LIMIT 10;";

        $vars = array(
            ':commit_hash'=>$commit_hash
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    public function getTotalInstallsToUpgrade($commit_hash) {
        $q  = "SELECT count(*) as total FROM user_routes WHERE is_active = 1 ";
        $q .= "AND commit_hash != :commit_hash;";

        $vars = array(
            ':commit_hash'=>$commit_hash
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }

    public function getStaleRoutes($count=25) {
        $q  = "SELECT * FROM user_routes WHERE is_active = 1 ";
        $q .= "AND last_dispatched < DATE_SUB(NOW(), INTERVAL 1 HOUR) ORDER BY last_dispatched ASC ";
        $q .= "LIMIT :limit;";

        $vars = array(
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    public function getStaleRoutes10kAndUp($count=25) {
        $q  = "SELECT * FROM user_routes WHERE is_active = 1 AND follower_count >= 10000 ";
        $q .= "AND last_dispatched < DATE_SUB(NOW(), INTERVAL 1 HOUR) ORDER BY last_dispatched ASC ";
        $q .= "LIMIT :limit;";

        $vars = array(
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    public function getStaleRoutes1kTo10k($count=25) {
        $q  = "SELECT * FROM user_routes WHERE is_active = 1 AND (follower_count < 10000 AND follower_count >= 1000) ";
        $q .= "AND last_dispatched < DATE_SUB(NOW(), INTERVAL 1 HOUR) ORDER BY last_dispatched ASC ";
        $q .= "LIMIT :limit;";

        $vars = array(
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    public function getRouteIDsNotInstalled($count=25) {
        $q  = "SELECT id FROM user_routes WHERE route = '' ";
        $q .= "LIMIT :limit;";

        $vars = array(
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    public function getStalestRoute1kTo10kLastDispatchTime() {
        $q  = "SELECT last_dispatched FROM user_routes WHERE is_active=1 ";
        $q .= "AND (follower_count < 10000 AND follower_count >= 1000) ";
        $q .= "ORDER BY last_dispatched ASC LIMIT 1";
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['last_dispatched'];
    }

    public function getStalestRoute10kAndUpLastDispatchTime() {
        $q  = "SELECT last_dispatched FROM user_routes WHERE is_active=1  AND follower_count >= 10000 ";
        $q .= "ORDER BY last_dispatched ASC LIMIT 1";
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['last_dispatched'];
    }

    public function getStalestRouteLastDispatchTime() {
        $q  = "SELECT last_dispatched FROM user_routes WHERE is_active=1 AND follower_count < 1000 ";
        $q .= "ORDER BY last_dispatched ASC LIMIT 1";
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['last_dispatched'];
    }

    public function getTotalActiveRoutes() {
        $q  = "SELECT count(*) AS total FROM user_routes WHERE is_active = 1;";
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }

    public function updateLastDispatchedTime($id) {
        $q  = "UPDATE user_routes SET last_dispatched = CURRENT_TIMESTAMP() ";
        $q .= "WHERE id = :id ";

        $vars = array(
            ':id'=>(int) $id
        );
        //echo self::mergeSQLVars($q, $vars)."\n";
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function updateCommitHash($id, $commit_hash) {
        $q  = "UPDATE user_routes SET commit_hash = :commit_hash ";
        $q .= "WHERE id = :id ";

        $vars = array(
            ':commit_hash'=>$commit_hash,
            ':id'=>(int) $id
        );
        //echo self::mergeSQLVars($q, $vars)."\n";
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function setActive($id, $is_active) {
        $q  = "UPDATE user_routes SET is_active = :is_active ";
        $q .= "WHERE id = :id ";

        $vars = array(
            ':is_active'=> (int) $is_active,
            ':id'=>(int) $id
        );
        //echo self::mergeSQLVars($q, $vars)."\n";
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function createOwner($email, $pwd, $is_admin=false) {
        $activation_code = rand(1000, 9999);
        $pwd_salt = $this->generateSalt($email);
        $api_key = $this->generateAPIKey();
        $hashed_pwd = $this->hashPassword($pwd, $pwd_salt);

        $q = "INSERT INTO tu_owners SET email=:email, pwd=:hashed_pwd, pwd_salt=:pwd_salt, joined=NOW(), ";
        $q .= "activation_code=:activation_code, full_name=:full_name, api_key=:api_key, is_activated=1 ";

        if ($is_admin) {
            $q .= ", is_admin=1";
        }
        $vars = array(
                ':email'=>$email,
                ':hashed_pwd'=>$hashed_pwd,
                ':pwd_salt'=>$pwd_salt,
                ':activation_code'=>$activation_code,
                ':full_name'=>'',
                ':api_key'=>$api_key
        );
        if ($this->profiler_enabled) Profiler::setDAOMethod(__METHOD__);
        $ps = $this->execute($q, $vars);
        return array($this->getInsertId($ps), $api_key);
    }

    public function insertLogEntry($user_route_id, $commit_hash, $success, $migration_message) {
        $q = "INSERT INTO install_log (user_route_id, commit_hash, migration_success, migration_message) VALUES ";
        $q .= "(:user_route_id, :commit_hash, :migration_success, :migration_message ); ";

        $vars = array(
            ':user_route_id'=>$user_route_id,
            ':commit_hash'=>$commit_hash,
            ':migration_success'=>$success,
            ':migration_message'=>$migration_message,
        );
        if ($this->profiler_enabled) Profiler::setDAOMethod(__METHOD__);
        $ps = $this->execute($q, $vars);
        return $this->getInsertId($ps);
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

    /**
     * Generate a new API KEY - md5 hashed random string
     * @return str A generated API Key
     */
    private function generateAPIKey() {
        return md5(uniqid(mt_rand(), true)); // generate random api key
    }

    public function insertInstance($network_user_id, $network_username, $network = "twitter", $viewer_id = false) {
        $q  = "INSERT INTO tu_instances ";
        $q .= "(network_user_id, network_username, network, network_viewer_id, last_post_id, is_public) ";
        $q .= "VALUES (:user_id , :username, :network, :viewer_id, '', 1) ";
        $vars = array(
            ':user_id'=>(string)$network_user_id,
            ':username'=>$network_username,
            ':network'=>$network,
            ':viewer_id'=>(string)($viewer_id ? $viewer_id : $network_user_id)
        );
        if ($this->profiler_enabled) Profiler::setDAOMethod(__METHOD__);
        $ps = $this->execute($q, $vars);
        return $this->getInsertId($ps);
    }

    public function insertOwnerInstance($owner_id, $instance_id, $oauth_token = '', $oauth_token_secret = '') {
        $q = "INSERT INTO tu_owner_instances
                (owner_id, instance_id, oauth_access_token, oauth_access_token_secret)
                    VALUES (:owner_id,:instance_id,:oauth_access_token,:oauth_access_token_secret)";

        $vars = array(':owner_id' => $owner_id,
                      ':instance_id' => $instance_id,
                      ':oauth_access_token' => $oauth_token,
                      ':oauth_access_token_secret' => $oauth_token_secret
        );
        if ($this->profiler_enabled) Profiler::setDAOMethod(__METHOD__);
        $stmt = $this->execute($q, $vars);
        if ( $this->getInsertCount($stmt) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function insertOptionValue($namespace, $option_name, $option_value) {
        $q = "INSERT INTO tu_options (namespace, option_name, option_value, last_updated, created)
        VALUES (:namespace, :option_name, :option_value, NOW(), NOW())";

        $vars = array(
          ':namespace' => $namespace,
          ':option_name' => $option_name,
          ':option_value' => $option_value
        );
        if ($this->profiler_enabled) Profiler::setDAOMethod(__METHOD__);
        $stmt = $this->execute($q, $vars);
        if ( $this->getInsertCount($stmt) > 0) {
            return true;
        } else {
            return false;
        }
    }
}