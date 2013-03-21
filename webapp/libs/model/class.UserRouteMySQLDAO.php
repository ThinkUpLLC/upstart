<?php
class UserRouteMySQLDAO extends PDODAO {

    public function insert($email, $twitter_username, $twitter_user_id, $oauth_access_token, $oauth_access_token_secret,
    $is_verified, $follower_count) {
        $q  = "INSERT INTO user_routes (email, twitter_username, twitter_user_id, oauth_access_token, ";
        $q .= "oauth_access_token_secret, is_verified, follower_count) VALUES (:email, ";
        $q .= ":twitter_username, :twitter_user_id, :oauth_access_token, :oauth_access_token_secret, ";
        $q .= ":is_verified, :follower_count); ";
        $vars = array(
            ':email'=>$email,
            ':twitter_username'=>$twitter_username,
            ':twitter_user_id'=>$twitter_user_id,
            ':oauth_access_token'=>$oauth_access_token,
            ':oauth_access_token_secret'=>$oauth_access_token_secret,
            ':is_verified'=>(integer) $is_verified,
            ':follower_count'=>(integer) $follower_count
        );
        //echo self::mergeSQLVars($q, $vars);
        try {
            $ps = $this->execute($q, $vars);
        } catch (PDOException $e) {
            $exception = $e->getMessage();
            if ((preg_match('/Duplicate entry/', $exception)>0) && (preg_match('/for key \'email\'/', $exception)>0)) {
                return 1;
            }
        }
        return $this->getUpdateCount($ps);
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

    public function getById($id) {
        $q  = "SELECT * FROM user_routes ";
        $q .= "WHERE id = :id ";
        $vars = array(
            ':id'=>$id
        );
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsArray($ps);
    }

    public function updateRoute($id, $route) {
        $cfg = Config::getInstance();
        $db_name = $cfg->getValue('db_name');
        $q  = "USE ".$db_name."; UPDATE user_routes SET route = :route ";
        $q .= "WHERE id = :id ";
        $vars = array(
            ':id'=>$id,
            ':route'=>$route
        );
        $ps = $this->execute($q, $vars);
    }

    public function getUserList($page_number=1, $count=50) {
        $start_on_record = ($page_number - 1) * $count;
        $q  = "SELECT * FROM user_routes ";
        $q .= "ORDER BY follower_count DESC, is_verified DESC ";
        $q .= "LIMIT :start_on_record, :limit;";

        $vars = array(
            ':start_on_record'=>$start_on_record,
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    private static function mergeSQLVars($sql, $vars) {
        foreach ($vars as $k => $v) {
            $sql = str_replace($k, (is_int($v))?$v:"'".$v."'", $sql);
        }
        $config = Config::getInstance();
        $prefix = $config->getValue('table_prefix');
        $gmt_offset = $config->getGMTOffset();
        $sql = str_replace('#gmt_offset#', $gmt_offset, $sql);
        $sql = str_replace('#prefix#', $prefix, $sql);
        return $sql;
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
        $q = "INSERT INTO tu_options (namespace, option_name, option_value)
        VALUES (:namespace, :option_name, :option_value)";

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
