<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

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
}