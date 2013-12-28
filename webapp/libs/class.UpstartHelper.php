<?php
class UpstartHelper {
    /**
     * Validate email address
     * This method uses a raw regex instead of filter_var because as of PHP 5.3.3,
     * filter_var($email, FILTER_VALIDATE_EMAIL) validates local email addresses.
     * From 5.2 to 5.3.3, it does not.
     * Therefore, this method uses the PHP 5.2 regex instead of filter_var in order to return consistent results
     * regardless of PHP version.
     * http://svn.php.net/viewvc/php/php-src/trunk/ext/filter/logical_filters.c?r1=297250&r2=297350
     *
     * @param str $email Email address to validate
     * @return bool Whether or not it's a valid address
     */
    public static function validateEmail($email = '') {
        //return filter_var($email, FILTER_VALIDATE_EMAIL));
        $reg_exp = "/^((\\\"[^\\\"\\f\\n\\r\\t\\b]+\\\")|([A-Za-z0-9_][A-Za-z0-9_\\!\\#\\$\\%\\&\\'\\*\\+\\-\\~\\".
        "/\\=\\?\\^\\`\\|\\{\\}]*(\\.[A-Za-z0-9_\\!\\#\\$\\%\\&\\'\\*\\+\\-\\~\\/\\=\\?\\^\\`\\|\\{\\}]*)*))@((\\".
        "[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.".
        "((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\\])|".
        "(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.".
        "((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|".
        "((([A-Za-z0-9])(([A-Za-z0-9\\-])*([A-Za-z0-9]))?(\\.(?=[A-Za-z0-9\\-]))?)+[A-Za-z]+))$/D";
        //return (preg_match($reg_exp, $email) === false)?false:true;
        return (preg_match($reg_exp, $email)>0)?true:false;
    }
    public static function validatePassword($passwd) {
        return (preg_match("/(?=.{8,})(?=.*[a-zA-Z])(?=.*[0-9])/", $passwd));
    }
    /**
     * Get application URL
     * @param bool $replace_localhost_with_ip
     * @param bool $use_filesystem_path Use filesystem path instead of path specified in config.inc.php
     * @return str application URL
     */
    public static function getApplicationURL($replace_localhost_with_ip = false, $use_filesystem_path = true,
    $should_url_encode = true) {
        $server = self::getApplicationHostName();
        if ($replace_localhost_with_ip) {
            $server = ($server == 'localhost')?'127.0.0.1':$server;
        }
        if ($use_filesystem_path) {
            $site_root_path = self::getSiteRootPathFromFileSystem();
        } else {
            $cfg = Config::getInstance();
            $site_root_path = $cfg->getValue('site_root_path');
        }
        if ($should_url_encode) {
            //URLencode everything except spaces in site_root_path
            $site_root_path = str_replace('%2f', '/', strtolower(urlencode($site_root_path)));
        }
        if  (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') { //non-standard port
            if (isset($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] == '443') { //account for standard https port
                $port = '';
            } else {
                $port = ':'.$_SERVER['SERVER_PORT'];
            }
        } else {
            $port = '';
        }
        return 'http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$server.$port.$site_root_path;
    }

    /**
     * Get the application's host name or server name, i.e., example.com.
     * @return str Host name either set by PHP global vars or stored in the database
     */
    public static function getApplicationHostName() {
        //First attempt to get the host name without querying the database
        //Try SERVER_NAME
        $server = empty($_SERVER['SERVER_NAME']) ? '' : $_SERVER['SERVER_NAME'];
        //Second, try HTTP_HOST
        if ($server == '' ) {
            $server = empty($_SERVER['HTTP_HOST']) ? '' : $_SERVER['HTTP_HOST'];
        }
        //Finally fall back to stored application setting set by Installer::storeServerName
        if ($server == '') {
            $option_dao = DAOFactory::getDAO('OptionDAO');
            $server_app_setting = $option_dao->getOptionByName(OptionDAO::APP_OPTIONS, 'server_name');
            if (isset($server_app_setting)) {
                $server = $server_app_setting->option_value;
            }
        }
        //domain name is always lowercase
        $server = strtolower($server);
        return $server;
    }

    public static function getSiteRootPathFromFileSystem() {
        $dirs_under_root = array('admin');
        if (isset($_SERVER['PHP_SELF'])) {
            $current_script_path = explode('/', $_SERVER['PHP_SELF']);
        } else {
            $current_script_path = array();
        }
        array_pop($current_script_path);
        if ( in_array( end($current_script_path), $dirs_under_root ) ) {
            array_pop($current_script_path);
        }
        $current_script_path = implode('/', $current_script_path) . '/';
        return $current_script_path;
    }

}