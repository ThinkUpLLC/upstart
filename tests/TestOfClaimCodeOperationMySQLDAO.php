<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfClaimCodeOperationMySQLDAO extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $dao = new ClaimCodeOperationMySQLDAO();
        $this->assertIsA($dao, 'ClaimCodeOperationMySQLDAO');
    }

    public function testInsertAndGet() {
        $dao = new ClaimCodeOperationMySQLDAO();
        //Insert an operation
        $code_operation = $dao->insert( 'asdfadfa_100', 'asdfadsf', 'buyer@example.com', 'Buyer Name', '100 USD',
            'SS', 'bundle 2014', 365);
        $code_operation = $dao->getByReferenceID('asdfadfa_100');

        $this->assertIsA($code_operation, 'ClaimCodeOperation');
        $this->assertEqual($code_operation->reference_id, 'asdfadfa_100');
        $this->assertEqual($code_operation->buyer_email, 'buyer@example.com');
        $this->assertEqual($code_operation->buyer_name, 'Buyer Name');
        $this->assertEqual($code_operation->transaction_amount, '100 USD');
        $this->assertEqual($code_operation->status_code, 'SS');
        $this->assertEqual($code_operation->type, 'bundle 2014');
        $this->assertEqual($code_operation->number_days, 365);
        $this->assertEqual($code_operation->transaction_id, 'asdfadsf');

        //Insert an operation with duplicate reference ID and status
        $this->expectException('DuplicateClaimCodeOperationException');
        $code_operation = $dao->insert( 'asdfadfa_100', 'asdfadsf', 'buyer@example.com', 'Buyer Name', '100 USD',
            'SS', 'bundle 2014', 365);
    }
}