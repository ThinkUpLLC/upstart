<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfClaimCodeMySQLDAO extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $dao = new ClaimCodeMySQLDAO();
        $this->assertIsA($dao, 'ClaimCodeMySQLDAO');
    }

    public function testInsertAndGetAndRedeemCode() {
        $dao = new ClaimCodeMySQLDAO();
        //Insert a code that gets generated
        $code = $dao->insert('better web bundle 2014', 203, 365);
        $claim_code = $dao->get($code);

        $this->assertIsA($claim_code, 'ClaimCode');
        $this->assertEqual($claim_code->is_redeemed, 0);
        $this->assertEqual($claim_code->operation_id, 203);
        $this->assertEqual($claim_code->type, 'better web bundle 2014');
        $this->assertNull($claim_code->redemption_date);

        //Try to insert the same code, should generate a new one
        $next_code = $dao->insert('better web bundle 2014', 607, 365, $code);
        $this->assertNotEqual($next_code, $code);

        $result = $dao->redeem($code);
        $this->assertEqual($result, 1);

        $claim_code = $dao->get($code);

        $this->assertIsA($claim_code, 'ClaimCode');
        $this->assertEqual($claim_code->is_redeemed, 1);
        $this->assertEqual($claim_code->operation_id, 203);
        $this->assertEqual($claim_code->type, 'better web bundle 2014');
        $this->assertNotNull($claim_code->redemption_date);
    }
}