<?php
/*********************************/
/***  UPSTART CUSTOM CONFIG    ***/
/*********************************/

$ISOSCELES_CFG['upstart_host']              = 'upstart.dev';

//Twitter
$ISOSCELES_CFG['oauth_consumer_key']        = '';
$ISOSCELES_CFG['oauth_consumer_secret']     = '';

//Facebook
$ISOSCELES_CFG['facebook_app_id']           = '';
$ISOSCELES_CFG['facebook_api_secret']       = '';
$ISOSCELES_CFG['facebook_max_crawl_time']   = 5;

//Expand URLS
$ISOSCELES_CFG['expandurls_links_to_expand_per_crawl']         = 10;

//Amazon Payments
$ISOSCELES_CFG['AWS_ACCESS_KEY_ID_DEPREC'] = '';
$ISOSCELES_CFG['AWS_SECRET_ACCESS_KEY_DEPREC'] = '';
$ISOSCELES_CFG['AWS_ACCESS_KEY_ID']         = '';
$ISOSCELES_CFG['AWS_SECRET_ACCESS_KEY']     = '';

$ISOSCELES_CFG['amazon_sandbox']            = true;
$ISOSCELES_CFG['amazon_payment_auth_validity_start']     = time();

//Mandrill
$ISOSCELES_CFG['mandrill_api_key']          = '';
$ISOSCELES_CFG['mandrill_notifications_template']           = 'ThinkUp LLC Member Insight Notifications';

//Dispatch
$ISOSCELES_CFG['dispatch_endpoint']         = 'https://upstart.dev/dispatch/';
$ISOSCELES_CFG['dispatch_auth_token']       = 'itisnicetobenice104';
$ISOSCELES_CFG['dispatch_socket']           = '/tmp/mysql.sock';
$ISOSCELES_CFG['dispatch_timezone']         = 'America/New_York';
$ISOSCELES_CFG['dispatch_http_username']    = '';
$ISOSCELES_CFG['dispatch_http_passwd']      = '';

//Insights Poster plugin
$ISOSCELES_CFG['insights_poster_twitter_consumer_key']        = '';
$ISOSCELES_CFG['insights_poster_twitter_consumer_secret']     = '';
$ISOSCELES_CFG['insights_poster_twitter_oauth_token_secret']  = '';
$ISOSCELES_CFG['insights_poster_twitter_oauth_token']         = '';

//Recurly
$ISOSCELES_CFG['recurly_subdomain'] = 'thinkup';
$ISOSCELES_CFG['recurly_api_key']   = '';

/************************************************/
/***  THINKUP USER INSTALL DATABASE CONFIG    ***/
/************************************************/

$ISOSCELES_CFG['tu_db_host']                   = 'localhost';
$ISOSCELES_CFG['tu_db_user']                   = 'tu_installuser';
$ISOSCELES_CFG['tu_db_password']               = '';
$ISOSCELES_CFG['tu_db_socket']                 = '';
$ISOSCELES_CFG['tu_db_port']                   = '';
//$ISOSCELES_CFG['tu_table_prefix']              = '';

//User installations
$ISOSCELES_CFG['app_source_path']           = $ISOSCELES_CFG['source_root_path'] . "webapp/sandbox/";
$ISOSCELES_CFG['master_app_source_path']    = "/var/www/default/thinkup-master/webapp";
$ISOSCELES_CFG['chameleon_app_source_path'] = "/var/www/default/thinkup-chameleon/webapp";
$ISOSCELES_CFG['data_path']                 = $ISOSCELES_CFG['source_root_path'] . 'webapp/sandbox/data/';
$ISOSCELES_CFG['admin_email']               = "admin@thinkup.com";
$ISOSCELES_CFG['admin_password']            = "h7BLSlz2Vt";
$ISOSCELES_CFG['admin_session_api_key']     = "D3sBgqVgdr";
$ISOSCELES_CFG['user_password']             = "ch4ng3m3";
$ISOSCELES_CFG['user_installation_url']     = "https://upstart.dev/sandbox/{user}/";
$ISOSCELES_CFG['user_installation_db_prefix'] = "thinkupstart_";

ini_set('error_reporting', E_ALL || E_STRICT);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
ini_set('memory_limit', '750M');

