<?php

require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';
//require_once ISOSCELES_PATH.'extlibs/simpletest/web_tester.php';
//require_once ISOSCELES_PATH.'extlibs/simpletest/mock_objects.php';


$all_tests = new TestSuite('Upstart tests');
$all_tests->add(new TestOfAuthorizationMySQLDAO());
$all_tests->add(new TestOfClickMySQLDAO());
$all_tests->add(new TestOfErrorLogMySQLDAO());
$all_tests->add(new TestOfInstallLogMySQLDAO());
$all_tests->add(new TestOfThinkUpTablesMySQLDAO());
$all_tests->add(new TestOfNewSubscriberController());
$all_tests->add(new TestOfSubscribeController());
$all_tests->add(new TestOfSubscriberAuthorizationMySQLDAO());
$all_tests->add(new TestOfAuthorizationMySQLDAO());
$all_tests->add(new TestOfSubscriberMySQLDAO());
$all_tests->add(new TestOfLoginController());
$all_tests->add(new TestOfForgotPasswordController());
$all_tests->add(new TestOfCheckUsernameController());
$all_tests->add(new TestOfChooseUsernameController());
$all_tests->add(new TestOfResetPasswordController());
$all_tests->add(new TestOfSettingsController());
$all_tests->add(new TestOfSessionCache());
$all_tests->add(new TestOfUpstartHelper());
$all_tests->add(new TestOfAppInstaller());
$all_tests->add(new TestOfAmazonFPSAPIAccessor());
$all_tests->add(new TestOfPaymentMySQLDAO());
$all_tests->add(new TestOfSubscriberPaymentMySQLDAO());

//$tr = new TextReporter();
//$all_tests->run( $tr );
