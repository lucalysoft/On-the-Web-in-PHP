<?php

class ViewModulelistmenuTest extends SuiteCRM\StateCheckerPHPUnitTestCaseAbstract
{
    public function test__construct()
    {
        // store state
        
        $state = new SuiteCRM\StateSaver();
        $state->pushGlobals();
        
        // test
        


        //execute the contructor and check for the Object type and options attribute
        $view = new ViewModulelistmenu();

        $this->assertInstanceOf('ViewModulelistmenu', $view);
        $this->assertInstanceOf('SugarView', $view);
        $this->assertTrue(is_array($view->options));
        
        // clean up
        
        $state->popGlobals();
    }

    public function testdisplay()
    {
        // store state
        
        $state = new SuiteCRM\StateSaver();
        $state->pushGlobals();
        
        // test
        

        //execute the method with required child objects preset. it should return some html. 
        $view = new ViewModulelistmenu();
        $view->ss = new Sugar_Smarty();

        ob_start();
        $view->display();
        $renderedContent = ob_get_contents();
        ob_end_clean();

        $this->assertGreaterThan(0, strlen($renderedContent));
        $this->assertEquals(false, is_array($renderedContent));
        
        // clean up
        
        $state->popGlobals();
    }
}
