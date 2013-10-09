<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfMandrillMailer extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testMail() {
        MandrillMailer::sendConfirmationEmail('ginatrapani+testmandrill@gmail.com', 'Greta Von Trapp',
        'http://example.com/confirm.php');
    }
}