<?php
class AppInstaller {
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
     * Chameleon copy of ThinkUp application files which run crawler.
     * @var str
     */
    var $chameleon_app_source_path;
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
     * User installation URL
     * @var str
     */
    var $user_installation_url;
    /**
     * Installation results message
     * @var str
     */
    var $results_message = "";
    /**
     * Prefix for archived installations
     * @var str
     */
    const ARCHIVED_DB_PREFIX = 'thinkupstop_';

    var $required_config_values = array(
        'app_source_path',
        'master_app_source_path',
        'chameleon_app_source_path',
        'data_path',
        'admin_email',
        'admin_email',
        'admin_password',
        'user_installation_url',
        'facebook_max_crawl_time',
        'dispatch_timezone',
        'db_host',
        //'dispatch_socket',
        //'db_port',
        'db_name',
        'admin_session_api_key',
        'oauth_consumer_key',
        'oauth_consumer_secret',
        'facebook_app_id',
        'facebook_api_secret',
        'facebook_max_crawl_time',
        'mandrill_notifications_template',
        'expandurls_links_to_expand_per_crawl',
    );

    public function __construct() {
        self::checkRequiredConfigValues();
        $cfg = Config::getInstance();
        // @TODO Verify all the directories exist, throw an Exception if not
        $this->app_source_path = $cfg->getValue('app_source_path');
        $this->master_app_source_path = $cfg->getValue('master_app_source_path');
        $this->chameleon_app_source_path = $cfg->getValue('chameleon_app_source_path');
        $this->data_path = $cfg->getValue('data_path');

        $this->admin_email = $cfg->getValue('admin_email');
        $this->admin_password = $cfg->getValue('admin_password');
        // @TODO Verify this string includes {user} in it and is URLish
        $this->user_installation_url = $cfg->getValue('user_installation_url');
    }

    private function checkRequiredConfigValues() {
        $cfg = Config::getInstance();
        foreach ($this->required_config_values as $req_value) {
            $value = $cfg->getValue($req_value);
            if ($value == null) {
                throw new InstallerMissingConfigValueException('Required value '.$req_value. ' is not set.');
            }
        }
    }

    public function install($subscriber_id) {
        $this->results_message = null;
        $commit_hash = self::getMasterInstallCommitHash();

        if (isset($this->app_source_path)
            && isset($this->master_app_source_path)
            && isset($this->data_path)
            && isset($this->admin_email)
            && isset($this->admin_password)
            && isset($this->user_installation_url)) {

            $subscriber = null;
            if (isset($subscriber_id)) {
                $subscriber_dao = new SubscriberMySQLDAO();
                $subscriber = $subscriber_dao->getById($subscriber_id);
            } else {
                throw new Exception('No subscriber specified');
            }

            if ($subscriber->thinkup_username == null) {
                throw new Exception("ThinkUp username is not set.");
            } elseif ($subscriber->date_installed != null && $subscriber->is_installation_active) {
                throw new Exception('Installation for '.$subscriber->thinkup_username.' already exists.');
            } else {
                session_write_close();
                // Set up application files
                self::setUpApplicationFiles($subscriber->thinkup_username);
                // Set up installation database
                $database_name = self::createDatabase($subscriber->thinkup_username);
                // Upgrade database
                $install_log_dao = new InstallLogMySQLDAO();
                self::upgradeDatabaseToLatestMigrations($subscriber, $commit_hash, $install_log_dao,
                $subscriber_dao);

                // Create session API token Upstart will use to log into ThinkUp via the Session API
                $api_key_private = hash('sha256', rand(). $subscriber->email);
                $subscriber->api_key_private = substr($api_key_private, 0, 32);

                self::setUpApplicationOptions($subscriber->thinkup_username);

                list($admin_id, $admin_api_key, $owner_id, $owner_api_key) =
                self::createOwners($subscriber);
                self::setUpServiceUser($owner_id, $subscriber);

                $url = str_replace ("{user}", $subscriber->thinkup_username, $this->user_installation_url);

                $subscriber_dao->intializeInstallation($subscriber_id, $subscriber->api_key_private,
                $commit_hash);
                self::logToUserMessage("Updated waitlist with link and db name");
                try {
                    self::dispatchCrawlJob($subscriber->thinkup_username);
                } catch (DispatchException $e) {
                    self::logToUserMessage('Crawl job not dispatched due to malformed JSON.');
                }
                $subscriber_dao->updateLastDispatchedTime($subscriber_id);
                $install_log_dao->insertLogEntry($subscriber_id, $commit_hash, 1, "Installed");

                self::logToUserMessage("Complete. Log in at <a href=\"$url\" target=\"new\">".$url."</a>.");
            }
            return $this->results_message;
        } else {
            throw new Exception("Sorry, the installer is not configured to run just yet. Yet!");
        }
    }

    /**
     * Uninstall a member's installation.
     * * Remove symlink and data directory
     * * Archive or drop database (depending on $do_archive_db)
     *
     * @param  int  $subscriber_id
     * @param  boolean $do_archive_db Whether or not to archive (vs delete) the database
     * @return str Log of uninstallation activity
     * @throws Exception If subscriber doesn't exist
     * @throws PDOException If there's a problem archiving or dropping the database
     * @throws InactiveInstallationException If is_installation_active is false
     * @throws NonExistentInstallationException If date_installed is null or ThinkUp username is not set
     */
    public function uninstall($subscriber_id, $do_archive_db = true) {
        $this->results_message = null;
        $subscriber = null;
        if (isset($subscriber_id)) {
            $subscriber_dao = new SubscriberMySQLDAO();
            $subscriber = $subscriber_dao->getById($subscriber_id);

            // Check if installation exists and is active
            if ($subscriber->thinkup_username == null) {
                throw new NonExistentInstallationException("ThinkUp username is not set.");
            } elseif ($subscriber->date_installed == null) {
                throw new NonExistentInstallationException($subscriber->thinkup_username .
                    ' installation date_installed is not set.');
            } elseif (!$subscriber->is_installation_active) {
                throw new InactiveInstallationException($subscriber->thinkup_username .
                    ' installation is not active (is_installation_active is set to false).');
            } else {
                // De-symlink directory
                $cmd = 'rm -rf '.$this->app_source_path.$subscriber->thinkup_username;
                $cmd_result = exec($cmd, $output, $return_var);
                self::logToUserMessage("De-symlinked user application directory - ".$cmd);

                // rm -rf data directory
                $cmd = 'rm -rf '.$this->data_path.$subscriber->thinkup_username;
                $cmd_result = exec($cmd, $output, $return_var);
                self::logToUserMessage("Deleted user data directory - ". $cmd);

                // Move database to archive
                self::dropDatabase($subscriber->thinkup_username, $do_archive_db);

                // Set subscriber commit_hash, date_installed, is_installation_active, last_dispatched to null
                $subscriber_dao->updateDateInstalled($subscriber_id, null);
                $subscriber_dao->setInstallationActive($subscriber_id, 0);
                $subscriber_dao->updateCommitHash($subscriber_id, null);
                $subscriber_dao->resetLastDispatchedTime($subscriber_id);
                self::logToUserMessage("Updated subscriber record");

                // Insert uninstallation record in install log
                $install_log_dao = new InstallLogMySQLDAO();
                $install_log_dao->insertLogEntry( $subscriber_id, $subscriber->commit_hash, 1, "Uninstalled");
                self::logToUserMessage("Uninstallation complete.");
            }
        } else {
            throw new Exception('No subscriber specified.');
        }
        return $this->results_message;
    }

    /**
     * Upgrade the user installation database to the latest migrations, the equivalent of running
     * upgrade.php --with-new-sql. We do this in case we're deploying code with migrations that haven't been rolled
     * into a release yet.
     * @param  Subscriber $subscriber
     * @param  str $commit_hash Commit has of master app source / user installation
     * @param  InstallLogMySQLDAO $install_log_dao
     * @param  SubscriberMySQLDAO $subscriber_dao
     * @throws Exception if unable to exec the upgrade command
     * @return void
     */
    protected function upgradeDatabaseToLatestMigrations(Subscriber $subscriber, $commit_hash,
        InstallLogMySQLDAO $install_log_dao, SubscriberMySQLDAO $subscriber_dao) {
        // Get in the right directory to exec the upgrade
        $cfg = Config::getInstance();
        $master_app_source_path = $cfg->getValue('chameleon_app_source_path');
        $cwd = getcwd();
        if (!chdir($master_app_source_path.'/install/cli/thinkupllc-chameleon-upgrader') ) {
            throw new Exception("Could not chdir to ".
            $master_app_source_path.'/install/cli/thinkupllc-chameleon-upgrader');
        }

        // Initialize upgrade call parameters that are the same for every installation
        /*
        * {"installation_name":"steveklabnik", "timezone":"America/Los_Angeles", "db_host":"localhost",
        * "db_name":"thinkupstart_steveklabnik", "db_socket":"/tmp/mysql.sock",  "db_port":""}
        */
       $prefix = $cfg->getValue('user_installation_db_prefix');
        $upgrade_params_array = array(
            'installation_name'=>$subscriber->thinkup_username,
            'timezone'=>$cfg->getValue('dispatch_timezone'),
            'db_host'=>$cfg->getValue('tu_db_host'),
            'db_name'=>$prefix.$subscriber->thinkup_username,
            'db_socket'=>$cfg->getValue('tu_db_socket'),
            'db_port'=>$cfg->getValue('tu_db_port')
        );
        $upgrade_params_json = json_encode($upgrade_params_array);

        // Capture returned JSON
        if (!exec("php upgrade.php '".$upgrade_params_json."'", $upgrade_status_json, $return_int) ) {
            throw new Exception('Unable to exec php upgrade.php '.$upgrade_params_json.
            "  Returned data was ". $return_int ." output " . Utils::varDumpToString($upgrade_status_json));
        }
        $upgrade_status_array = JSONDecoder::decode($upgrade_status_json[0], true);
        //Now that the upgrade is done, go back to the original working directory
        chdir($cwd);

        // DEBUG start
        // echo "php upgrade.php '".$upgrade_params_json."'";
        // print_r($upgrade_status_array);
        // DEBUG end

        if ($upgrade_status_array['migration_success'] === true) {
            $install_log_dao->insertLogEntry($subscriber->id, $commit_hash, 1,
            $upgrade_status_array['migration_message']);
        } else {
            // If error, set inactive, and store message, status, commit in install_log
            $subscriber_dao->setInstallationActive($subscriber->id, 0);
            $install_log_dao->insertLogEntry($subscriber->id, $commit_hash, 0,
            $upgrade_status_array['migration_message']);
        }
    }

    protected function logToUserMessage($message) {
        $this->results_message .= "<li>".$message."</li>";
    }

    /**
     * Create the user installation with a symlink in app_source_path to the master_app_source path.
     * Symlink the user installation data directory in data_path.
     * @param str $path Folder name of user installation (usually ThinkUp username)
     * @throws Exception If master_app_source is not a directory, if symlink was not created, if data dir symlink was
     *         not created
     */
    protected function setUpApplicationFiles($path) {
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
            self::logToUserMessage("Symlinked new ThinkUp installation at ". $this->app_source_path.$path);
        } else {
            $result = Utils::varDumpToString($output);
            throw new Exception("Could not create symlink from ".$this->master_app_source_path." to ".
            $this->app_source_path.$path. "<br>Command: ".$cmd. "<br> Command output: ".$result . "<br>Return var ".
            $return_var. "<br>Command result ".$cmd_result);
        }
        $cmd = 'mkdir '. $this->data_path . $path;
        exec($cmd);
        if (is_dir($this->data_path . $path )) {
            self::logToUserMessage("Created new data directory " . $this->data_path . $path);
            return true;
        } else {
            throw new Exception("Could not create new data directory ".$this->data_path . $path);
        }
    }

    private function getMoveQueries($thinkup_username, $time_suffix) {
        $install_pdo = self::getInstallPDO();
        $prefix = Config::getInstance()->getValue('user_installation_db_prefix');
        $q = "SELECT concat('RENAME TABLE ".$prefix.$thinkup_username.".',table_name, ' TO `".
            self::ARCHIVED_DB_PREFIX.$thinkup_username. "-".$time_suffix."`.',table_name, ';') as move_command ";
        $q .= "FROM information_schema.TABLES WHERE table_schema='".
            $prefix.$thinkup_username."';";
        $ps = $install_pdo->query($q);
        $ps->setFetchMode(PDO::FETCH_ASSOC);
        $rows = $ps->fetchAll();
        $ps->closeCursor();
        $ps = null;
        $install_pdo = null;
        return $rows;
    }

    /**
     * Drop or archive a member's database.
     *
     * @param  str  $thinkup_username Member's username
     * @param  boolean $do_keep_copy  Whether or not to keep a copy of the database
     * @return void
     * @throws PDOException If there's a problem creating or dropping the database
     */
    public function dropDatabase($thinkup_username, $do_keep_copy = true) {
        $prefix = Config::getInstance()->getValue('user_installation_db_prefix');
        $install_pdo = self::getInstallPDO();
        $time_suffix = time();

        if ($do_keep_copy) {
            $queries = self::getMoveQueries($thinkup_username, $time_suffix);

            //create database to archive tables to
            $install_pdo->exec('CREATE DATABASE `'.self::ARCHIVED_DB_PREFIX.$thinkup_username.'-'.$time_suffix.'`');

            foreach ($queries as $q) {
                $install_pdo->exec($q['move_command']);
            }
            self::logToUserMessage("Moved user database ".$prefix.$thinkup_username." to ".
                self::ARCHIVED_DB_PREFIX.$thinkup_username.$time_suffix);
        }
        //drop original database
        $install_pdo->exec('DROP DATABASE '.$prefix.$thinkup_username);

        //explicitly close this connection
        $uninstall_pdo = null;
        self::logToUserMessage("Dropped user database ".$prefix.$thinkup_username);
    }

    /**
     * Create user installation database.
     * @param  str $thinkup_username Name of database (usually the user's ThinkUp username)
     * @return str Newly-created database name
     * @throws PDOException
     */
    protected function createDatabase($thinkup_username) {
        $install_pdo = self::getInstallPDO();
        $prefix = Config::getInstance()->getValue('user_installation_db_prefix');
        $q = "CREATE DATABASE ".$prefix.$thinkup_username.";";
        $install_pdo->exec($q);
        //explicitly close this connection
        $install_pdo = null;

        $dao = new ThinkUpTablesMySQLDAO($thinkup_username);
        $query_file = $this->master_app_source_path . '/install/sql/build-db_mysql.sql';
        $q = file_get_contents($query_file);
        ThinkUpPDODAO::$PDO->exec($q);

        self::logToUserMessage("Created new database ".$prefix.$thinkup_username);
        return $prefix.$thinkup_username;
    }
    /**
     * Get PDO connection to installation server without choosing a database because it hasn't been created yet.
     * @return PDO
     */
    private function getInstallPDO() {
        $config = Config::getInstance();
        $install_pdo = new PDO(
            self::getConnectString(),
            $config->getValue('tu_db_user'),
            $config->getValue('tu_db_password') );
        $install_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $install_pdo->setAttribute(PDO::ATTR_PERSISTENT, false);
        return $install_pdo;
    }
    /**
     * Generates a connect string without an existing database name to use when creating a database.
     * @return string PDO connect string
     */
    protected static function getConnectString() {
        $config = Config::getInstance();
        //set default db type to mysql if not set
        $db_type = $config->getValue('db_type');
        if (! $db_type) { $db_type = 'mysql'; }
        $db_socket = $config->getValue('tu_db_socket');
        if ( !$db_socket) {
            $db_port = $config->getValue('tu_db_port');
            if (!$db_port) {
                $db_socket = '';
            } else {
                $db_socket = ";port=".$config->getValue('tu_db_port');
            }
        } else {
            $db_socket=";unix_socket=".$db_socket;
        }
        $db_string = sprintf(
            "%s:host=%s%s",
        $db_type,
        $config->getValue('tu_db_host'),
        $db_socket
        );
        return $db_string;
    }

    /**
     * Set any required application options, like server_name.
     * @param str $thinkup_username User installation username.
     * @return void
     */
    protected function setUpApplicationOptions($thinkup_username) {
        $server_name = self::getInstallationServerName($thinkup_username);
        $tu_tables_dao = new ThinkUpTablesMySQLDAO($thinkup_username);
        $tu_tables_dao->insertOptionValue( 'application_options', 'server_name', $server_name);
        self::logToUserMessage("Added server_name application option (".$server_name.")");
    }

    /**
     * Get the servername for the user installation. For subdomain setups, this should be username.thinkup.com.
     * For subdirectory setups, this should be example.com.
     * @param  str $thinkup_username ThinkUp username
     * @return str Installation server name
     */
    public function getInstallationServerName($thinkup_username){
        $server_name = str_replace ("{user}", $thinkup_username, $this->user_installation_url);
        $server_name = str_replace ("http://", '', $server_name);
        $server_name = str_replace ("https://", '', $server_name);
        if (strpos($server_name, '/') !== false) {
            $split_server_name = explode('/', $server_name);
            $server_name = $split_server_name[0];
        }
        $server_name = str_replace ("/", '', $server_name);
        return $server_name;
    }

    protected function createOwners(Subscriber $subscriber) {
        $tu_tables_dao = new ThinkUpTablesMySQLDAO($subscriber->thinkup_username);
        //insert admin into owners
        list($admin_pwd_salt, $admin_hashed_pwd) = $tu_tables_dao->saltAndHashPwd($this->admin_email,
            $this->admin_password);

        $cfg = Config::getInstance();
        $admin_session_api_key = $cfg->getValue('admin_session_api_key');
        list($admin_id, $admin_api_key) = $tu_tables_dao->createOwner($this->admin_email, $admin_hashed_pwd,
            $admin_pwd_salt, null, 'America/New_York', true, $admin_session_api_key);

        //insert user into owners
        list($user_id, $user_api_key) = $tu_tables_dao->createOwner($subscriber->email, $subscriber->pwd,
            $subscriber->pwd_salt, $subscriber->membership_level, $subscriber->timezone, false,
            $subscriber->api_key_private);

        self::logToUserMessage("Inserted $this->admin_email and user ".$subscriber->email);
        return array($admin_id, $admin_api_key, $user_id, $user_api_key);
    }

    protected function setUpServiceUser($owner_id, $subscriber) {
        $tu_tables_dao = new ThinkUpTablesMySQLDAO($subscriber->thinkup_username);
        if ($subscriber->network_user_id != null) {
            //insert Twitter or Facebook user into instances
            $network_user_name =($subscriber->network=='twitter')?$subscriber->network_user_name:$subscriber->full_name;
            $instance_id = $tu_tables_dao->insertInstance($subscriber->network_user_id, $network_user_name,
                $subscriber->network, $subscriber->network_user_id);

            //associate owner with Twitter user in owner_instances and add auth tokens
            $tu_tables_dao->insertOwnerInstance($owner_id, $instance_id, $subscriber->oauth_access_token,
            $subscriber->oauth_access_token_secret);

            self::logToUserMessage("Inserted ".$subscriber->network." account and associated with ".
                $subscriber->email."");
        }

        // Add Twitter consumer key/secret info to options
        $cfg = Config::getInstance();
        $oauth_consumer_key = $cfg->getValue('oauth_consumer_key');
        $oauth_consumer_secret = $cfg->getValue('oauth_consumer_secret');
        $tu_tables_dao->insertOptionValue('plugin_options-1', 'oauth_consumer_key', $oauth_consumer_key);
        $tu_tables_dao->insertOptionValue('plugin_options-1', 'oauth_consumer_secret', $oauth_consumer_secret);

        // Add Facebook API keys to options
        $facebook_app_id = $cfg->getValue('facebook_app_id');
        $facebook_api_secret = $cfg->getValue('facebook_api_secret');
        $facebook_max_crawl_time = $cfg->getValue('facebook_max_crawl_time');
        $tu_tables_dao->insertOptionValue('plugin_options-2', 'facebook_app_id', $facebook_app_id);
        $tu_tables_dao->insertOptionValue('plugin_options-2', 'facebook_api_secret', $facebook_api_secret);
        // Add Facebook crawl time cap to options
        $tu_tables_dao->insertOptionValue('plugin_options-2', 'max_crawl_time', $facebook_max_crawl_time);

        // Add Mandrill template name to options
        $mandrill_notifications_template = $cfg->getValue('mandrill_notifications_template');
        $tu_tables_dao->insertOptionValue('plugin_options-6', 'mandrill_template',
        $mandrill_notifications_template);

        // Add Expand URLs cap to options
        $links_to_expand_per_crawl = $cfg->getValue('expandurls_links_to_expand_per_crawl');
        $tu_tables_dao->insertOptionValue('plugin_options-5', 'links_to_expand',
        $links_to_expand_per_crawl);
    }

    protected function dispatchCrawlJob($thinkup_username) {
        $cfg = Config::getInstance();
        $jobs_array = array();
        $jobs_array[] = array(
            'installation_name'=>$thinkup_username,
            'timezone'=>$cfg->getValue('dispatch_timezone'),
            'db_host'=>$cfg->getValue('tu_db_host'),
            'db_name'=>$cfg->getValue('user_installation_db_prefix').$thinkup_username,
            'db_socket'=>$cfg->getValue('tu_db_socket'),
            'db_port'=>$cfg->getValue('tu_db_port'),
            'high_priority'=>'true'
        );
        if (!UpstartHelper::isTest()) {
            // call Dispatcher
            $result_decoded = Dispatcher::dispatch($jobs_array);
            self::logToUserMessage("Dispatched crawl job: " .Utils::varDumpToString($result_decoded));
        } else {
            self::logToUserMessage("Didn't dispatch crawl job since this is a test");
        }
    }

    /**
     * Get the commit hash of the master app source.
     * @return str git commit hash
     */
    public function getMasterInstallCommitHash() {
        return self::getCommitHash($this->master_app_source_path);
    }

    /**
     * Get the commit hash of the chamelon app source.
     * @return str git commit hash
     */
    public function getChameleonInstallCommitHash() {
        return self::getCommitHash($this->chameleon_app_source_path);
    }

    /**
     * Get the commit hash of the git repository in a given directory.
     * @param str $path Location of git repository
     * @return str git commit hash
     */
    private function getCommitHash($path) {
        $cur_dir = getcwd();
        chdir($path);
        exec('git rev-parse --verify HEAD 2> /dev/null', $output);
        $commit_hash = $output[0];
        chdir($cur_dir);
        return $commit_hash;
    }
}