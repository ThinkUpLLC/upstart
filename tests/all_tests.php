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
$all_tests->add(new TestOfSessionCache());

//$tr = new TextReporter();
//$all_tests->run( $tr );
