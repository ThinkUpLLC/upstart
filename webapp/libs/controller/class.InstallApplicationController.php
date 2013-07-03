<?php
class InstallApplicationController extends Controller {
    /**
     * Parent folder where the application source files for each installation will go.
     * @var str
     */
    var $app_source_path;
    /**
     * Master copy of ThinkUp application files which will be copied/symlinked for each installation.
     * @var str
     */
    var $master_app_source_path;
    /**
     * Parent folder where the application data files for each installation will go.
     * @var str
     */
    var $data_path;
    /**
     * Administrator email address.
     * @var str
     */
    var $admin_email;
    /**
     * Default admin password.
     * @var str
     */
    var $admin_password;
    /**
     * Default user password.
     * @var str
     */
    var $user_password;
    /**
     * Base URL for installations.
     * @var str
     */
    var $url_base;

    public function control() {
        $this->setContentType('text/html; charset=UTF-8');
        $this->setViewTemplate('install.run-top.tpl');
        echo $this->generateView();

        $cfg = Config::getInstance();
        $this->app_source_path = $cfg->getValue('app_source_path');
        $this->master_app_source_path = $cfg->getValue('master_app_source_path');
        $this->data_path = $cfg->getValue('data_path');
        $this->admin_email = $cfg->getValue('admin_email');
        $this->admin_password = $cfg->getValue('admin_password');
        $this->user_password = $cfg->getValue('user_password');
        $this->url_base = $cfg->getValue('url_base');

        if (isset($this->app_source_path)
        && isset($this->master_app_source_path)
        && isset($this->data_path)
        && isset($this->admin_email)
        && isset($this->admin_password)
        && isset($this->user_password)
        && isset($this->url_base)) {

            $route = null;
            if (isset($_GET['id'])) {
                $dao = new UserRouteMySQLDAO();
                $route = $dao->getById($_GET['id']);
            }
            if (!isset($route)) {
                if (isset($_GET['id'])) {
                    self::output('User route doesn\'t exist');
                } else {
                    self::output('No user route specified');
                }
            } else {
                if ($route['route'] == '') {
                    try {
                        session_write_close();
                        $code = self::setUpAppFiles($route['twitter_username']);
                        $database_name = self::createDatabase($code);
                        list($admin_id, $admin_api_key, $owner_id, $owner_api_key) = self::createUsers($route['email']);
                        self::setUpServiceUser($owner_id, $route);

                        $url = $this->url_base.strtolower($code)."/";

                        $dao->updateRoute($_GET['id'], $url, $database_name, $is_active=1);
                        self::output("Updated waitlist with link and db name");

                        $crawler_url = $url.'crawler/rss.php?un='.urlencode($this->admin_email).'&as='
                        .urlencode($admin_api_key);
                        self::output("Ran crawler, make sure insights are generated ($crawler_url).");
                        self::output(self::getURLContents($crawler_url));

                        self::output("Complete. Log in at <a href=\"$url\" target=\"new\">".$url."</a>.");
                    } catch (Exception $e) {
                        self::output($e->getMessage());
                    }
                } else {
                    self::output('Installation exists at <a href="'.$route['route'].' target="new">'.$route['route'].
                "</a>.");
                }
            }
        } else {
            self::output('Sorry, the installer is not configured to run just yet. Yet!');
        }
        $this->setViewTemplate('install.run-bottom.tpl');
        echo $this->generateView();
    }

    protected function output($message) {
        echo "<li>".$message."</li>";
    }

    protected function setUpAppFiles($path) {
        if (is_dir ($this->app_source_path . $path )) {
            $unique = uniqid();
            $path .= substr($unique, strlen($unique)-4, strlen($unique));
        }
        if (!is_dir($this->master_app_source_path)) {
            throw new Exception($this->master_app_source_path . " is not a directory.");
        }
        $cmd = 'ln -s '.$this->master_app_source_path.' '.$this->app_source_path.$path;
        $cmd_result = exec($cmd, $output, $return_var);
        if (is_link($this->app_source_path.$path )) {
            self::output("Symlinked new ThinkUp installation at $path");
        } else {
            $result = Utils::varDumpToString($output);
            throw new Exception("Could not create symlink from ".$this->master_app_source_path." to ".
            $this->app_source_path.$path. "<br>Command: ".$cmd. "<br> Command output: ".$result . "<br>Return var ".
            $return_var. "<br>Command result ".$cmd_result);
        }
        $cmd = 'mkdir '. $this->data_path . $path;
        exec($cmd);
        if (is_dir($this->data_path . $path )) {
            self::output("Created new data directory " . $this->data_path . $path);
        } else {
            throw new Exception("Could not create new data directory");
        }
        return $path;
    }

    protected function createDatabase($code) {
        $q = "CREATE DATABASE thinkupstart_$code; USE thinkupstart_$code;";
        PDODAO::$PDO->exec($q);

        $query_file = $this->master_app_source_path . '/install/sql/build-db_mysql.sql';
        $q = file_get_contents($query_file);
        PDODAO::$PDO->exec($q);

        self::output("Created new database thinkupstart_$code");
        return "thinkupstart_$code";
    }

    protected function createUsers($email) {
        $dao = new UserRouteMySQLDAO();
        //insert admin into owners
        list($admin_id, $admin_api_key) = $dao->createOwner($this->admin_email, $this->admin_password, true);

        //insert user into owners
        list($user_id, $user_api_key) = $dao->createOwner($email, $this->user_password);

        self::output("Inserted $this->admin_email password $this->admin_password and user ".$email.
        " with password $this->user_password");
        return array($admin_id, $admin_api_key, $user_id, $user_api_key);
    }

    protected function setUpServiceUser($owner_id, $route) {
        $dao = new UserRouteMySQLDAO();
        //insert Twitter user into instances
        $instance_id = $dao->insertInstance($route['twitter_user_id'], $route['twitter_username']);

        //associate owner with Twitter user in owner_instances and add auth tokens
        $dao->insertOwnerInstance($owner_id, $instance_id, $route['oauth_access_token'],
        $route['oauth_access_token_secret']);

        self::output("Inserted Twitter account and associate with ".$route['email']."");

        //add consumer key info to options
        $cfg = Config::getInstance();
        $oauth_consumer_key = $cfg->getValue('oauth_consumer_key');
        $oauth_consumer_secret = $cfg->getValue('oauth_consumer_secret');

        //add app keys to options table
        $dao->insertOptionValue('plugin_options-1', 'oauth_consumer_key', $oauth_consumer_key);
        $dao->insertOptionValue('plugin_options-1', 'oauth_consumer_secret', $oauth_consumer_secret);
    }

    /**
     * Get the contents of a URL via GET
     * @param str $URL
     * @return str contents
     */
    public static function getURLContents($URL) {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);

        //echo "URL: ".$URL."\n";
        //echo $contents;
        //echo "STATUS: ".$status."\n";
        if (isset($contents)) {
            return $contents;
        } else {
            return null;
        }
    }

}