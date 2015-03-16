<?php
require_once dirname(__FILE__) . '/init.tests.php';

class TestOfUpstartHelper extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testGetApplicationUrl() {
        $cfg = Config::getInstance();
        $app_url = $cfg->getValue('upstart_host');
        $site_root_path = $cfg->getValue('site_root_path');
        $result = UpstartHelper::getApplicationURL();
        $this->debug($result);
        $this->assertEqual($result, 'http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$app_url.$site_root_path);
    }

    public function testIsUsernameValid() {
        //valid letters only
        $this->assertTrue(UpstartHelper::isUsernameValid('valid'));
        //valid numbers only
        $this->assertTrue(UpstartHelper::isUsernameValid('1234567'));
        //valid numbers and letters
        $this->assertTrue(UpstartHelper::isUsernameValid('valid1456'));
        //valid numbers and letters and underscores
        $this->assertTrue(UpstartHelper::isUsernameValid('i_am_valid_yo'));
        //too long
        $this->assertFalse(UpstartHelper::isUsernameValid('validusernamebutitiswaytoolongmorethan15characters'));
        //too short
        $this->assertFalse(UpstartHelper::isUsernameValid('va'));
        //special char
        $this->assertFalse(UpstartHelper::isUsernameValid('%aesfa'));
        //underscore
        $this->assertTrue(UpstartHelper::isUsernameValid('a_a'));
        //start with underscore
        $this->assertTrue(UpstartHelper::isUsernameValid('_addafasdf'));
        //dash
        $this->assertFalse(UpstartHelper::isUsernameValid('a-a'));
        //space
        $this->assertFalse(UpstartHelper::isUsernameValid('aes fa'));
    }

    public function testValidateEmail() {
        //do validate valid public internet addresses
        $this->assertTrue(UpstartHelper::validateEmail('h@bit.ly'));
        $this->assertTrue(UpstartHelper::validateEmail('you@example.com'));
        $this->assertTrue(UpstartHelper::validateEmail('youfirstname.yourlastname@example.co.uk'));
        //don't validate local addresses
        $this->assertFalse(UpstartHelper::validateEmail('yaya@yaya'));
        $this->assertFalse(UpstartHelper::validateEmail('me@localhost'));
        $this->assertFalse(UpstartHelper::validateEmail('kevin@tedxbuffalo'));
        //don't validate addresses with invalid chars
        $this->assertFalse(UpstartHelper::validateEmail('yaya'));
        $this->assertFalse(UpstartHelper::validateEmail('me@localhost@notavalidaddress'));
        $this->assertFalse(UpstartHelper::validateEmail('me@local host'));
        $this->assertFalse(UpstartHelper::validateEmail('me@local#host'));

    }

    public function testIsStage() {
        $_SERVER['SERVER_NAME'] = 'stage.thinkup.com';
        $this->assertTrue(UpstartHelper::isStage());
        $_SERVER['SERVER_NAME'] = 'thinkup.com';
        $this->assertFalse(UpstartHelper::isStage());
    }
}