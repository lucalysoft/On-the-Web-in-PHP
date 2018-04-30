<?php

class ViewDetailTest extends SuiteCRM\StateCheckerPHPUnitTestCaseAbstract
{
    public function testViewDetail()
    {
        
        // save state
        
        $state = new SuiteCRM\StateSaver();
        $state->pushGlobals();
        
        // test 
        
        //execute the contructor and check for the Object type and type attribute
        $view = new ViewDetail();
        $this->assertInstanceOf('ViewDetail', $view);
        $this->assertInstanceOf('SugarView', $view);
        $this->assertAttributeEquals('detail', 'type', $view);
        
        // clean up
        
        $state->popGlobals();
    }

    public function testpreDisplay()
    {
        
        // save state
        
        $state = new SuiteCRM\StateSaver();
        $state->pushGlobals();
        
        // test 
        
        //execute the method with required attributes preset, it will initialize the dv(detail view) attribute. 
        $view = new ViewDetail();
        $view->module = 'Users';
        $view->bean = new User();
        $view->ss = new Sugar_Smarty();
        $view->preDisplay();
        $this->assertInstanceOf('DetailView2', $view->dv);
        $this->asserttrue(is_array($view->dv->defs));

        //execute the method again for a different module with required attributes preset, it will initialize the dv(detail view) attribute.
        $view = new ViewDetail();
        $view->module = 'Meetings';
        $view->bean = new Meeting();
        $view->ss = new Sugar_Smarty();
        $view->preDisplay();
        $this->assertInstanceOf('DetailView2', $view->dv);
        $this->asserttrue(is_array($view->dv->defs));
        
        // clean up
        
        $state->popGlobals();
    }

    public function testdisplay()
    {
        
        // save state
        
        $state = new SuiteCRM\StateSaver();
        $state->pushGlobals();
        
        // test 

        //execute the method with essential parameters set. it should return some html.
        $view = new ViewDetail();
        $view->module = 'Users';
        $view->bean = new User();
        $view->bean->id = 1;
        $view->ss = new Sugar_Smarty();
        $view->preDisplay();

        ob_start();
        $view->display();
        $renderedContent = ob_get_contents();
        ob_end_clean();
        $this->assertGreaterThan(0, strlen($renderedContent));
        
        // clean up
        
        $state->popGlobals();
    }
}
