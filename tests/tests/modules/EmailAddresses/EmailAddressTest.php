<?php


class EmailAddressTest extends SuiteCRM\StateCheckerPHPUnitTestCaseAbstract
{
    public function testEmailAddress()
    {
        $this->markTestIncomplete('Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::eapm"');
        
        // save state
        
        $state = new \SuiteCRM\StateSaver();
        $state->pushTable('email_addresses');
        $state->pushTable('eapm');
        
        // test
        
        //execute the contructor and check for the Object type and  attributes
        $email = new EmailAddress();
        $this->assertInstanceOf('EmailAddress', $email);
        $this->assertInstanceOf('SugarEmailAddress', $email);
        $this->assertInstanceOf('SugarBean', $email);

        $this->assertAttributeEquals('EmailAddresses', 'module_dir', $email);
        $this->assertAttributeEquals('EmailAddresses', 'module_name', $email);
        $this->assertAttributeEquals('EmailAddress', 'object_name', $email);
        $this->assertAttributeEquals('email_addresses', 'table_name', $email);

        $this->assertAttributeEquals(true, 'disable_row_level_security', $email);
        
        // clean up
        
        $state->popTable('eapm');
        $state->popTable('email_addresses');
    }

    public function testsave()
    {
        $this->markTestIncomplete('Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::eapm"');
        
        // save state
        
        $state = new \SuiteCRM\StateSaver();
        $state->pushTable('email_addresses');
        $state->pushTable('eapm');
        
        // test
        
        $email = new EmailAddress();

        $email->email_address = 'test@test.com';
        $email->invaid_email = 0;

        $email->save();

        //test for record ID to verify that record is saved
        $this->assertTrue(isset($email->id));
        $this->assertEquals(36, strlen($email->id));

        //mark the record as deleted and verify that this record cannot be retrieved anymore.
        $email->mark_deleted($email->id);
        $result = $email->retrieve($email->id);
        $this->assertEquals(null, $result);
        
        // clean up
        
        $state->popTable('eapm');
        $state->popTable('email_addresses');
    }
}
