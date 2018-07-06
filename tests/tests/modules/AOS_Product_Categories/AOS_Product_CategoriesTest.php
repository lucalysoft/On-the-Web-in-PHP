<?php

class AOS_Product_CategoriesTest extends SuiteCRM\StateCheckerPHPUnitTestCaseAbstract
{    
    protected function storeStateAll() 
    {

        // save state
        
        $state = new SuiteCRM\StateSaver();
        $state->pushTable('aod_indexevent');
        $state->pushTable('emails');
        $state->pushTable('aos_product_categories');
        $state->pushTable('config');
        $state->pushTable('emails_text');
        $state->pushTable('inbound_email_autoreply');
        $state->pushFile('config.php');
        $state->pushGlobals();
        
        
        return $state;
    }
    
    protected function restoreStateAll($state) 
    {

        // clean up
        
        $state->popGlobals();
        $state->popFile('config.php');
        $state->popTable('inbound_email_autoreply');
        $state->popTable('emails_text');
        $state->popTable('config');
        $state->popTable('aos_product_categories');
        $state->popTable('emails');
        $state->popTable('aod_indexevent');
        
    }
    
    public function testAOS_Product_Categories()
    {
        // save state
        
        $state = $this->storeStateAll();
        
        // test
        


        //execute the contructor and check for the Object type and  attributes
        $aosProductCategories = new AOS_Product_Categories();
        $this->assertInstanceOf('AOS_Product_Categories', $aosProductCategories);
        $this->assertInstanceOf('Basic', $aosProductCategories);
        $this->assertInstanceOf('SugarBean', $aosProductCategories);

        $this->assertAttributeEquals('AOS_Product_Categories', 'module_dir', $aosProductCategories);
        $this->assertAttributeEquals('AOS_Product_Categories', 'object_name', $aosProductCategories);
        $this->assertAttributeEquals('aos_product_categories', 'table_name', $aosProductCategories);
        $this->assertAttributeEquals(true, 'new_schema', $aosProductCategories);
        $this->assertAttributeEquals(true, 'disable_row_level_security', $aosProductCategories);
        $this->assertAttributeEquals(true, 'importable', $aosProductCategories);
        
        
        // clean up
        
        $this->restoreStateAll($state);
    }

    public function testsave()
    {
        // save state
        
        $state = $this->storeStateAll();
        
        // test
        

        $aosProductCategories = new AOS_Product_Categories();
        $aosProductCategories->name = 'test';
        $aosProductCategories->parent_category_id = 1;

        $aosProductCategories->save();

        //test for record ID to verify that record is saved
        $this->assertTrue(isset($aosProductCategories->id));
        $this->assertEquals(36, strlen($aosProductCategories->id));

        //mark the record as deleted and verify that this record cannot be retrieved anymore.
        $aosProductCategories->mark_deleted($aosProductCategories->id);
        $result = $aosProductCategories->retrieve($aosProductCategories->id);
        $this->assertEquals(null, $result);
        
        
        // clean up
        
        $this->restoreStateAll($state);
    }
}
