<?php

class ViewClassicTest extends SuiteCRM\StateCheckerPHPUnitTestCaseAbstract
{
    public function test__construct()
    {
        // state save
        
        $state = new SuiteCRM\StateSaver();
        $state->pushGlobals();
        
        // test
        
        //execute the contructor and check for the Object type and type attribute

        //test with no paramerters
        $view = new ViewClassic();
        $this->assertInstanceOf('ViewClassic', $view);
        $this->assertInstanceOf('SugarView', $view);
        $this->assertAttributeEquals('', 'type', $view);

        //test with bean parameter;
        $bean = new User();
        $view = new ViewClassic($bean);
        $this->assertInstanceOf('ViewClassic', $view);
        $this->assertInstanceOf('SugarView', $view);
        $this->assertAttributeEquals('', 'type', $view);
        
        // clean up
        
        $state->popGlobals();
    }

    public function testdisplay()
    {
        // state save
        
        $state = new SuiteCRM\StateSaver();
        $state->pushGlobals();
        
        // test
        

        //test with a valid module but invalid action. it should return false.
        $view = new ViewClassic();
        $view->module = 'Home';
        $view->action = '';
        $ret = $view->display();
        $this->assertFalse($ret);

        //test with a valid module and uncustomized action. it should return true
        $view = new ViewClassic();
        $view->module = 'Home';
        $view->action = 'About';

        ob_start();
        $ret = $view->display();
        $renderedContent = ob_get_contents();
        ob_end_clean();
        $this->assertGreaterThan(0, strlen($renderedContent));
        $this->assertTrue($ret);

        //test with a valid module and customized action. it should return true
        $view = new ViewClassic();
        $view->module = 'Home';
        $view->action = 'index';

        ob_start();
        $ret = $view->display();
        $renderedContent = ob_get_contents();
        ob_end_clean();
        $this->assertGreaterThan(0, strlen($renderedContent));
        $this->assertTrue($ret);
        
        // clean up
        
        $state->popGlobals();
    }
}
