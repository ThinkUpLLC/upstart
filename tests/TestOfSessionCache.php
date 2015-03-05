<?php
require_once dirname(__FILE__) . '/init.tests.php';

class TestOfSessionCache extends UpstartUnitTestCase {

    public function setUp(){
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testPutGetIsset() {
        $config = Config::getInstance();

        //nothing is set
        $this->assertNull(SessionCache::get('my_key'));
        $this->assertFalse(SessionCache::isKeySet('my_key'));

        //set a key
        SessionCache::put('my_key', 'my_value');

        $this->assertTrue(isset($_SESSION[$config->getValue('source_root_path')]));
        $this->assertEqual($_SESSION[$config->getValue('source_root_path')]['my_key'], 'my_value');

        $this->assertEqual(SessionCache::get('my_key'), 'my_value');

        //overwrite existing key
        SessionCache::put('my_key', 'my_value2');
        $this->assertTrue($_SESSION[$config->getValue('source_root_path')]['my_key'] != 'my_value');
        $this->assertEqual($_SESSION[$config->getValue('source_root_path')]['my_key'], 'my_value2');

        //set another key
        SessionCache::put('my_key2', 'my_other_value');
        $this->assertEqual($_SESSION[$config->getValue('source_root_path')]['my_key2'], 'my_other_value');

        //unset first key
        SessionCache::unsetKey('my_key');
        $this->assertNull(SessionCache::get('my_key'));
        $this->assertFalse(SessionCache::isKeySet('my_key'));
    }

    public function testClearAllKeys() {
        $config = Config::getInstance();

        //nothing is set
        $this->assertNull(SessionCache::get('my_key'));
        $this->assertFalse(SessionCache::isKeySet('my_key'));

        //set a key
        SessionCache::put('my_key', 'my_value');
        SessionCache::put('my_second_key', 'my_value');
        SessionCache::put('my_third_key', 'my_value');

        $this->assertTrue(isset($_SESSION[$config->getValue('source_root_path')]));
        $this->assertEqual($_SESSION[$config->getValue('source_root_path')]['my_key'], 'my_value');
        $this->assertTrue(SessionCache::isKeySet('my_key'));

        SessionCache::clearAllKeys();
        $this->assertFalse(isset($_SESSION[$config->getValue('source_root_path')]['my_key']));
        $this->assertFalse(SessionCache::isKeySet('my_key'));
    }

}