<?php
class UserRouteMySQLDAO extends PDODAO {
    public function insert($email, $pwd) {
        $q  = "INSERT INTO user_routes ";
        $q .= "(email) ";
        $q .= "VALUES ( :email) ";
        $vars = array(
            ':email'=>$email,
        );
        try {
            $ps = $this->execute($q, $vars);
        } catch (PDOException $e) {
            $exception = $e->getMessage();
            if ((preg_match('/Duplicate entry/', $exception)>0) && (preg_match('/for key \'email\'/', $exception)>0)) {
                return $this->get($email);
            }
        }
        return $this->getInsertId($ps);
    }

    public function update($id, $twitter_username, $twitter_user_id, $oauth_access_token, $oauth_access_token_secret,
    $is_verified, $follower_count) {
        $q  = "UPDATE user_routes SET ";
        $q .= "twitter_username = :twitter_username, twitter_user_id = :twitter_user_id, ";
        $q .= "oauth_access_token = :oauth_access_token, oauth_access_token_secret = :oauth_access_token_secret, ";
        $q .= "is_verified = :is_verified, follower_count = :follower_count ";
        $q .= "WHERE id = :id ";
        $vars = array(
            ':id'=>$id,
            ':twitter_username'=>$twitter_username,
            ':twitter_user_id'=>$twitter_user_id,
            ':oauth_access_token'=>$oauth_access_token,
            ':oauth_access_token_secret'=>$oauth_access_token_secret,
            ':is_verified'=>(integer) $is_verified,
            ':follower_count'=>(integer) $follower_count
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function get($email) {
        $q  = "SELECT id FROM user_routes ";
        $q .= "WHERE email = :email ";
        $vars = array(
            ':email'=>$email
        );
        $ps = $this->execute($q, $vars);
        $result = $this->getDataRowAsArray($ps);
        return $result['id'];
    }

    public function delete($id) {
        $q  = "DELETE from #prefix#callbacks WHERE id=:id;";
        $vars = array(
            ':id'=>$id
        );
        $ps = $this->execute($q, $vars);
        return $this->getDeleteCount($ps);
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
}
