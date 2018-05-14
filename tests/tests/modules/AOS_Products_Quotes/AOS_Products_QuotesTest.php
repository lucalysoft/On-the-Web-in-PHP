<?php

class AOS_Products_QuotesTest extends SuiteCRM\StateCheckerPHPUnitTestCaseAbstract
{
    public function testAOS_Products_Quotes()
    {
        // save state
        
        $state = new SuiteCRM\StateSaver();
        $state->pushTable('roles_users');
        $state->pushGlobals();
        
        // test
        

        //execute the contructor and check for the Object type and  attributes
        $aosProductsQuotes = new AOS_Products_Quotes();
        $this->assertInstanceOf('AOS_Products_Quotes', $aosProductsQuotes);
        $this->assertInstanceOf('Basic', $aosProductsQuotes);
        $this->assertInstanceOf('SugarBean', $aosProductsQuotes);

        $this->assertAttributeEquals('AOS_Products_Quotes', 'module_dir', $aosProductsQuotes);
        $this->assertAttributeEquals('AOS_Products_Quotes', 'object_name', $aosProductsQuotes);
        $this->assertAttributeEquals('aos_products_quotes', 'table_name', $aosProductsQuotes);
        $this->assertAttributeEquals(true, 'new_schema', $aosProductsQuotes);
        $this->assertAttributeEquals(true, 'disable_row_level_security', $aosProductsQuotes);
        $this->assertAttributeEquals(true, 'importable', $aosProductsQuotes);
        
        // clean up
        
        $state->popGlobals();
        $state->popTable('roles_users');
    }

    public function testsave_lines()
    {
        $this->markTestIncomplete('Failed asserting that 4 matches expected 2.');
        // save state
        
        $state = new SuiteCRM\StateSaver();
        $state->pushTable('aos_products_quotes');
        $state->pushGlobals();
        
        // test
        

        $aosProductsQuotes = new AOS_Products_Quotes();

        //populate required values
        $post_data = array();
        $post_data['name'] = array('test1', 'test2');
        $post_data['group_number'] = array('1', '2');
        $post_data['product_id'] = array('1', '1');
        $post_data['product_unit_price'] = array(100, 200);

        //create parent bean
        $aosQuote = new AOS_Quotes();
        $aosQuote->id = 1;

        $aosProductsQuotes->save_lines($post_data, $aosQuote);

        //get the linked beans and verify if records created
        $product_quote_lines = $aosQuote->get_linked_beans('aos_products_quotes', $aosQuote->object_name);
        $this->assertEquals(count($post_data['name']), count($product_quote_lines));
        
        // clean up
        
        $state->popGlobals();
        $state->popTable('aos_products_quotes');
    }

    public function testmark_lines_deleted()
    {
        $this->markTestIncomplete('Failed asserting that 4 matches expected 2.');
        // save state
        
        $state = new SuiteCRM\StateSaver();
        $state->pushTable('aos_products_quotes');
        $state->pushGlobals();
        
        // test
        
        $aosProductsQuotes = new AOS_Products_Quotes();

        //create parent bean
        $aosQuote = new AOS_Quotes();
        $aosQuote->id = 1;

        //get the linked beans and get record count before deletion
        $product_quote_lines = $aosQuote->get_linked_beans('aos_products_quotes', $aosQuote->object_name);
        $expected = count($product_quote_lines);
        $product_quote_lines = null;

        $aosProductsQuotes->mark_lines_deleted($aosQuote);
        unset($aosQuote);

        //get the linked beans and get record count after deletion
        $aosQuote = new AOS_Quotes();
        $aosQuote->id = 1;
        $product_quote_lines = $aosQuote->get_linked_beans('aos_products_quotes', $aosQuote->object_name);
        $actual = count($product_quote_lines);

        $this->assertLessThan($expected, $actual);
        
        // clean up
        
        $state->popGlobals();
        $state->popTable('aos_products_quotes');
    }

    public function testsave()
    {

        // save state
        
        $state = new \SuiteCRM\StateSaver();
        $state->pushTable('aos_products_quotes');
        $state->pushTable('aos_line_item_groups');
        $state->pushTable('roles_users');
        
        // test
        
        $aosProductsQuotes = new AOS_Products_Quotes();

        $aosProductsQuotes->name = 'test';
        $aosProductsQuotes->product_id = 1;
        $aosProductsQuotes->product_unit_price = 100;

        $aosProductsQuotes->save();

        //test for record ID to verify that record is saved
        $this->assertTrue(isset($aosProductsQuotes->id));
        $this->assertEquals(36, strlen($aosProductsQuotes->id));

        //mark the record as deleted and verify that this record cannot be retrieved anymore.
        $aosProductsQuotes->mark_deleted($aosProductsQuotes->id);
        $result = $aosProductsQuotes->retrieve($aosProductsQuotes->id);
        $this->assertEquals(null, $result);
        
        // clean up
        
        $state->popTable('roles_users');
        $state->popTable('aos_line_item_groups');
        $state->popTable('aos_products_quotes');
    }
}
