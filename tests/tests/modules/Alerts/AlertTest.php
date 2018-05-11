<?php


class AlertTest extends SuiteCRM\StateCheckerPHPUnitTestCaseAbstract
{
    public function testAlert()
    {
        $this->markTestIncomplete("Incorrect state hash (in PHPUnitTest): Hash doesn't match at key \"database::oauth_tokens\".");
        
        // save state
        
        $state = new SuiteCRM\StateSaver();
        $state->pushTable('oauth_tokens');
        $state->pushTable('roles_users');
        
        // test

        //execute the contructor and check for the Object type and type attribute
        $alert = new Alert();
        $this->assertInstanceOf('Alert', $alert);
        $this->assertInstanceOf('Basic', $alert);
        $this->assertInstanceOf('SugarBean', $alert);

        $this->assertAttributeEquals('Alerts', 'module_dir', $alert);
        $this->assertAttributeEquals('Alert', 'object_name', $alert);
        $this->assertAttributeEquals('alerts', 'table_name', $alert);
        $this->assertAttributeEquals(true, 'new_schema', $alert);
        $this->assertAttributeEquals(true, 'disable_row_level_security', $alert);
        $this->assertAttributeEquals(false, 'importable', $alert);
        
        // clean up
        
        $state->popTable('roles_users');
        $state->popTable('oauth_tokens');
    }

    public function testbean_implements()
    {
        $this->markTestIncomplete("Incorrect state hash (in PHPUnitTest): Hash doesn't match at key \"database::oauth_tokens\".");
        
        // save state
        
        $state = new SuiteCRM\StateSaver();
        $state->pushTable('roles_users');
        
        // test

        $alert = new Alert();

        $this->assertEquals(false, $alert->bean_implements('')); //test with empty value
        $this->assertEquals(false, $alert->bean_implements('test')); //test with invalid value
        $this->assertEquals(true, $alert->bean_implements('ACL')); //test with valid value
        
        // clean up
        
        $state->popTable('roles_users');
    }
}
