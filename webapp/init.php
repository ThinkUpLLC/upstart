<?php
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

//Manually require external libraries
require_once 'extlibs/twitteroauth/twitteroauth.php';
require_once 'extlibs/twitteroauth/OAuth.php';

require_once 'extlibs/facebook/base_facebook.php';
require_once 'extlibs/facebook/facebook.php';


require_once 'extlibs/amazon/FPS/Interface.php';

$config = Config::getInstance();
if ($config->getValue('amazon_sandbox') === true) {
    require_once 'extlibs/amazon/CBUI/CBUIPipeline.sandbox.php';
    require_once 'extlibs/amazon/FPS/Client.sandbox.php';
} else {
    require_once 'extlibs/amazon/CBUI/CBUIPipeline.php';
    require_once 'extlibs/amazon/FPS/Client.php';
}

require_once 'extlibs/amazon/CBUI/CBUIRecurringTokenPipeline.php';
require_once 'extlibs/amazon/FPS/Exception.php';
require_once 'extlibs/amazon/FPS/Model.php';
require_once 'extlibs/amazon/FPS/Model/VerifySignatureRequest.php';
require_once 'extlibs/amazon/FPS/Model/VerifySignatureResponse.php';
require_once 'extlibs/amazon/FPS/Model/VerifySignatureResult.php';
require_once 'extlibs/amazon/FPS/Model/ResponseMetadata.php';
require_once 'extlibs/amazon/FPS/Model/Amount.php';
require_once 'extlibs/amazon/FPS/Model/PayRequest.php';
require_once 'extlibs/amazon/FPS/Model/PayResponse.php';
require_once 'extlibs/amazon/FPS/Model/PayResult.php';
require_once 'extlibs/amazon/FPS/Model/GetTransactionStatusRequest.php';
require_once 'extlibs/amazon/FPS/Model/GetTransactionStatusResponse.php';
require_once 'extlibs/amazon/FPS/Model/GetTransactionStatusResult.php';
require_once 'extlibs/amazon/IpnReturnUrlValidation/SignatureUtilsForOutbound.php';


require_once 'extlibs/mandrill/Mandrill.php';
