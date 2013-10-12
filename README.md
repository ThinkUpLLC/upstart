Upstart
=======

ThinkUp.com's subscriber and installation management system.

Configuration:
--------------
Set the following required configuration variables in webapp/extlibs/isosceles/libs/config.inc.php:

```
// Twitter
$ISOSCELES_CFG['oauth_consumer_key']
$ISOSCELES_CFG['oauth_consumer_secret']

//Facebook
$ISOSCELES_CFG['facebook_app_id']
$ISOSCELES_CFG['facebook_api_secret']

//Mailchimp
$ISOSCELES_CFG['mailchimp_api']
$ISOSCELES_CFG['mailchimp_list_id']

//User installations
$ISOSCELES_CFG['app_source_path']
$ISOSCELES_CFG['master_app_source_path']
$ISOSCELES_CFG['chameleon_app_source_path']
$ISOSCELES_CFG['data_path']
$ISOSCELES_CFG['admin_email']
$ISOSCELES_CFG['admin_password']
$ISOSCELES_CFG['user_password']
$ISOSCELES_CFG['user_installation_url']     = "http://{user}.upstart.com/sandbox/";

//Amazon Payments
$ISOSCELES_CFG['AWS_ACCESS_KEY_ID']
$ISOSCELES_CFG['AWS_SECRET_ACCESS_KEY']
$ISOSCELES_CFG['amazon_payment_auth_validity_start']     = date("U", mktime(12, 0, 0, 10, 10, 2013));

//Mandrill
$ISOSCELES_CFG['mandrill_api_key']

//Dispatch
$ISOSCELES_CFG['dispatch_endpoint'] = 'https://www.thinkup.com/dispatch/';
$ISOSCELES_CFG['dispatch_auth_token']
$ISOSCELES_CFG['dispatch_socket']
$ISOSCELES_CFG['dispatch_timezone']
$ISOSCELES_CFG['dispatch_http_username']
$ISOSCELES_CFG['dispatch_http_passwd']
```

Testing Amazon Payments
-----------------------
To use the Amazon Payments sandbox, search for "sandbox" and set the sandbox endpoints in
 
* /FPS/Client.php
* /CBUI/CBUIPipeline.php
