<?php
putenv("MODE=TESTS");
require_once 'config.tests.inc.php';

//set up 3 required constants
if ( !defined('ROOT_PATH') ) {
    define('ROOT_PATH', str_replace("\\",'/', dirname(dirname(__FILE__))) .'/');
}

if ( !defined('WEBAPP_PATH') ) {
    define('WEBAPP_PATH', ROOT_PATH . 'webapp/');
}

if ( !defined('TESTS_RUNNING') ) {
    define('TESTS_RUNNING', true);
}

//Register our lazy class loader
require_once ROOT_PATH.'webapp/extlibs/isosceles/libs/class.Loader.php';
//echo 'path to DAO: ' . ROOT_PATH . 'webapp/libs/dao/
//';

//Include test runner
require_once ROOT_PATH.'tests/simpletest/autorun.php';

Loader::register(array(
ROOT_PATH . 'tests/',
ROOT_PATH . 'tests/classes/',
ROOT_PATH . 'tests/fixtures/',
ROOT_PATH . 'webapp/libs/',
ROOT_PATH . 'webapp/libs/model/',
ROOT_PATH . 'webapp/libs/dao/',
ROOT_PATH . 'webapp/libs/controller/',
ROOT_PATH . 'webapp/libs/exceptions/'
));


require_once WEBAPP_PATH.'extlibs/amazon/FPS/Interface.php';

$config = Config::getInstance();
if ($config->getValue('amazon_sandbox') === true) {
    require_once WEBAPP_PATH.'extlibs/amazon/CBUI/CBUIPipeline.sandbox.php';
    require_once WEBAPP_PATH.'extlibs/amazon/FPS/Client.sandbox.php';
} else {
    require_once WEBAPP_PATH.'extlibs/amazon/CBUI/CBUIPipeline.php';
    require_once WEBAPP_PATH.'extlibs/amazon/FPS/Client.php';
}

require_once WEBAPP_PATH.'extlibs/amazon/CBUI/CBUIRecurringTokenPipeline.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Exception.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model/VerifySignatureRequest.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model/VerifySignatureResponse.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model/VerifySignatureResult.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model/ResponseMetadata.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model/Amount.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model/PayRequest.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model/PayResponse.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model/PayResult.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model/GetTransactionStatusRequest.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model/GetTransactionStatusResponse.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model/GetTransactionStatusResult.php';
require_once WEBAPP_PATH.'extlibs/amazon/simplepay/ButtonGenerator.php';
require_once WEBAPP_PATH.'extlibs/amazon/simplepay/SignatureUtils.php';

require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model/CancelSubscriptionAndRefundRequest.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model/CancelSubscriptionAndRefundResponse.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model/CancelSubscriptionAndRefundResult.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model/RefundRequest.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model/RefundResponse.php';
require_once WEBAPP_PATH.'extlibs/amazon/FPS/Model/RefundResult.php';

require_once WEBAPP_PATH.'extlibs/mandrill/Mandrill.php';

//require_once WEBAPP_PATH.'extlibs/recurly-client-php-2.4.1/lib/recurly.php';
require_once ROOT_PATH . 'tests/classes/mock.Recurly.php';
