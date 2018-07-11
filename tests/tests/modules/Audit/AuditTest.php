<?php

require_once 'modules/Audit/Audit.php';
class AuditTest extends SuiteCRM\StateCheckerPHPUnitTestCaseAbstract
{
    public function testAudit()
    {

        //execute the contructor and check for the Object type and  attributes
        $audit = new Audit();
        $this->assertInstanceOf('Audit', $audit);
        $this->assertInstanceOf('SugarBean', $audit);
        $this->assertAttributeEquals('Audit', 'module_dir', $audit);
        $this->assertAttributeEquals('Audit', 'object_name', $audit);
    }

    public function testget_summary_text()
    {
        $audit = new Audit();

        //test without setting name
        $this->assertEquals(null, $audit->get_summary_text());

        //test with name set
        $audit->name = 'test';
        $this->assertEquals('test', $audit->get_summary_text());
    }

    public function testcreate_export_query()
    {
        $this->markTestIncomplete('method has no implementation');
        
        $audit = new Audit();

        //execute the method and test if it works and does not throws an exception.
        try {
            $audit->create_export_query('', '');
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->fail("\nException: " . get_class($e) . ": " . $e->getMessage() . "\nin " . $e->getFile() . ':' . $e->getLine() . "\nTrace:\n" . $e->getTraceAsString() . "\n");
        }

    }

    public function testfill_in_additional_list_fields()
    {
        $this->markTestIncomplete('method has no implementation');
        
        $audit = new Audit();
        //execute the method and test if it works and does not throws an exception.
        try {
            $audit->fill_in_additional_list_fields();
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->fail("\nException: " . get_class($e) . ": " . $e->getMessage() . "\nin " . $e->getFile() . ':' . $e->getLine() . "\nTrace:\n" . $e->getTraceAsString() . "\n");
        }

    }

    public function testfill_in_additional_detail_fields()
    {

        $this->markTestIncomplete('method has no implementation');
        
        $audit = new Audit();
        //execute the method and test if it works and does not throws an exception.
        try {
            $audit->fill_in_additional_detail_fields();
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->fail("\nException: " . get_class($e) . ": " . $e->getMessage() . "\nin " . $e->getFile() . ':' . $e->getLine() . "\nTrace:\n" . $e->getTraceAsString() . "\n");
        }
    }

    public function testfill_in_additional_parent_fields()
    {

        $this->markTestIncomplete('method has no implementation');
        
        $audit = new Audit();
        //execute the method and test if it works and does not throws an exception.
        try {
            $audit->fill_in_additional_parent_fields();
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->fail("\nException: " . get_class($e) . ": " . $e->getMessage() . "\nin " . $e->getFile() . ':' . $e->getLine() . "\nTrace:\n" . $e->getTraceAsString() . "\n");
        }
    }

    public function testget_list_view_data()
    {

        $this->markTestIncomplete('method has no implementation');
        
        $audit = new Audit();
        //execute the method and test if it works and does not throws an exception.
        try {
            $audit->get_list_view_data();
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->fail("\nException: " . get_class($e) . ": " . $e->getMessage() . "\nin " . $e->getFile() . ':' . $e->getLine() . "\nTrace:\n" . $e->getTraceAsString() . "\n");
        }
    }

    public function testget_audit_link()
    {

        $this->markTestIncomplete('method has no implementation');
        
        $audit = new Audit();
        //execute the method and test if it works and does not throws an exception.
        try {
            $audit->get_audit_link();
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->fail("\nException: " . get_class($e) . ": " . $e->getMessage() . "\nin " . $e->getFile() . ':' . $e->getLine() . "\nTrace:\n" . $e->getTraceAsString() . "\n");
        }
    }

    public function testget_audit_list()
    {
        global $focus;
        $focus = new Account(); //use audit enabbled module object

        $audit = new Audit();

        //execute the method and verify that it returns an array
        $result = $audit->get_audit_list();
        $this->assertTrue(is_array($result));
    }

    public function testgetAssociatedFieldName()
    {
        global $focus;
        $focus = new Account(); //use audit enabbled module object

        $audit = new Audit();

        //test with name field
        $result = $audit->getAssociatedFieldName('name', '1');
        $this->assertEquals('1', $result);

        //test with parent_id field
        $result = $audit->getAssociatedFieldName('parent_id', '1');
        $this->assertEquals(null, $result);
    }
}
