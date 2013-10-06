<?php
ini_set('error_reporting', E_ALL || E_STRICT);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

if ( version_compare(PHP_VERSION, '5.2', '<') ) {
    exit("ERROR: ThinkUp requires PHP 5.2 or greater. The current version of PHP is ".PHP_VERSION.".");
}

//Register our lazy class loader
require_once 'extlibs/isosceles/libs/model/class.Loader.php';
Loader::register(array(
dirname(__FILE__).'/libs/',
dirname(__FILE__).'/libs/model/',
dirname(__FILE__).'/libs/controller/',
dirname(__FILE__).'/libs/dao/',
dirname(__FILE__).'/libs/exceptions/',
dirname(__FILE__).'/extlibs/twitteroauth/',
dirname(__FILE__).'/extlibs/mailchimp/'
));
require_once 'extlibs/twitteroauth/twitteroauth.php';
require_once 'extlibs/twitteroauth/OAuth.php';

require_once 'extlibs/amazon/CBUI/CBUIRecurringTokenPipeline.php';

require_once 'extlibs/amazon/FPS/Exception.php';
require_once 'extlibs/amazon/FPS/Interface.php';
require_once 'extlibs/amazon/FPS/Client.php';
require_once 'extlibs/amazon/FPS/Model.php';
require_once 'extlibs/amazon/FPS/Model/VerifySignatureRequest.php';
require_once 'extlibs/amazon/FPS/Model/VerifySignatureResponse.php';
require_once 'extlibs/amazon/FPS/Model/VerifySignatureResult.php';
require_once 'extlibs/amazon/FPS/Model/ResponseMetadata.php';
