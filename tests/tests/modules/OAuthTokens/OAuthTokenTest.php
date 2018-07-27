<?php

class OAuthTokenTest extends SuiteCRM\StateCheckerPHPUnitTestCaseAbstract
{
    public function test__construct()
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        

        //execute the contructor and check for the Object type and  attributes
        $oauthToken = new OAuthToken();

        $this->assertInstanceOf('OAuthToken', $oauthToken);
        $this->assertInstanceOf('SugarBean', $oauthToken);

        $this->assertAttributeEquals('OAuthTokens', 'module_dir', $oauthToken);
        $this->assertAttributeEquals('OAuthToken', 'object_name', $oauthToken);
        $this->assertAttributeEquals('oauth_tokens', 'table_name', $oauthToken);

        $this->assertAttributeEquals(true, 'disable_row_level_security', $oauthToken);
    }

    public function testsetState()
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        
        $this->markTestIncomplete('OAuthToken has not REQUEST!!');
        
        $oauthToken = new OAuthToken();
        $oauthToken->setState($oauthToken->REQUEST);

        $this->assertEquals($oauthToken->REQUEST, $oauthToken->tstate);
    }

    public function testsetConsumer()
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        
        $oauthToken = new OAuthToken();

        $oauthKey = new OAuthKey();
        $oauthKey->id = '1';

        $oauthToken->setConsumer($oauthKey);

        $this->assertEquals($oauthKey->id, $oauthToken->consumer);
        $this->assertEquals($oauthKey, $oauthToken->consumer_obj);
    }

    public function testsetCallbackURL()
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        
        $oauthToken = new OAuthToken();

        $url = 'test url';
        $oauthToken->setCallbackURL($url);

        $this->assertEquals($url, $oauthToken->callback_url);
    }

    public function testgenerate()
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        
        $result = OAuthToken::generate();

        $this->assertInstanceOf('OAuthToken', $result);
        $this->assertGreaterThan(0, strlen($result->token));
        $this->assertGreaterThan(0, strlen($result->secret));
    }

    public function testSaveAndOthers()
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        
        $oauthToken = OAuthToken::generate();

        $oauthToken->save();

        //test for record ID to verify that record is saved
        $this->assertTrue(isset($oauthToken->id));
        $this->assertEquals(12, strlen($oauthToken->id));

        //test load method
        $this->load($oauthToken->id);

        //test invalidate method
        $token = OAuthToken::load($oauthToken->id);
        $this->invalidate($token);

        //test authorize method
        $this->authorize($token);

        //test mark_deleted method
        $this->mark_deleted($oauthToken->id);
    }

    public function load($id)
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        
        $token = OAuthToken::load($id);

        $this->assertInstanceOf('OAuthToken', $token);
        $this->assertTrue(isset($token->id));
    }

    public function invalidate($token)
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        
        $token->invalidate();

        $this->assertEquals($token::INVALID, $token->tstate);
        $this->assertEquals(false, $token->verify);
    }

    public function authorize($token)
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        
        $result = $token->authorize('test');
        $this->assertEquals(false, $result);

        $token->tstate = $token::REQUEST;
        $result = $token->authorize('test');

        $this->assertEquals('test', $token->authdata);
        $this->assertGreaterThan(0, strlen($result));
        $this->assertEquals($result, $token->verify);
    }

    public function mark_deleted($id)
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        
        $this->markTestIncomplete('Token has not id but the test trying to asserting with it');
        
        $oauthToken = new OAuthToken();

        //execute the method
        $oauthToken->mark_deleted($id);

        //verify that record can not be loaded anymore
        $token = OAuthToken::load($id);
        $this->assertEquals(null, $token->id);
    }

    public function testcreateAuthorized()
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        
        $this->markTestIncomplete('Failed asserting that 4 matches expected 2.');
        // save state
        
        $state = new SuiteCRM\StateSaver();
        $state->pushTable('oauth_tokens');
//        $state->pushGlobals();
        
        // test
        
        $oauthKey = new OAuthKey();
        $oauthKey->id = '1';

        $user = new User();
        $user->retrieve('1');

        $oauthToken = OAuthToken::createAuthorized($oauthKey, $user);

        $this->assertEquals($oauthKey->id, $oauthToken->consumer);
        $this->assertEquals($oauthKey, $oauthToken->consumer_obj);
        $this->assertEquals($oauthToken::ACCESS, $oauthToken->tstate);
        $this->assertEquals($user->id, $oauthToken->assigned_user_id);

        //execute copyAuthData method
        $oauthToken->authdata = 'test';
        $this->copyAuthData($oauthToken);

        //finally mark deleted for cleanup
        $oauthToken->mark_deleted($oauthToken->id);
        
        // clean up
        
//        $state->popGlobals();
        $state->popTable('oauth_tokens');
    }

    public function copyAuthData($token)
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        
        $oauthToken = new OAuthToken();

        $oauthToken->copyAuthData($token);
        $this->assertEquals($token->authdata, $oauthToken->authdata);
        $this->assertEquals($token->assigned_user_id, $oauthToken->assigned_user_id);
    }

    public function testqueryString()
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        
        $this->markTestIncomplete("??? Incorrect state hash (in PHPUnitTest): Hash doesn't match at key \"database::oauth_tokens\".");
        
        // save state
        
        $state = new SuiteCRM\StateSaver();
        $state->pushTable('oauth_tokens');
        
        // test
        
        $oauthToken = new OAuthToken();

        $result = $oauthToken->queryString();
        $this->assertEquals('oauth_token=&oauth_token_secret=', $result);

        //test with attributes set
        $oauthToken->token = 'toekn';
        $oauthToken->secret = 'secret';
        $result = $oauthToken->queryString();
        $this->assertEquals('oauth_token=toekn&oauth_token_secret=secret', $result);
        
        // clean up
        
        $state->popTable('oauth_tokens');
    }

    public function testcleanup()
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        
        $this->markTestIncomplete("??? Incorrect state hash (in PHPUnitTest): Hash doesn't match at key \"database::oauth_tokens\".");
        
        // save state
        
        $state = new SuiteCRM\StateSaver();
        $state->pushTable('oauth_tokens');
        
        // test
        

        //execute the method and test if it works and does not throws an exception.
        try {
            OAuthToken::cleanup();
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->fail("\nException: " . get_class($e) . ": " . $e->getMessage() . "\nin " . $e->getFile() . ':' . $e->getLine() . "\nTrace:\n" . $e->getTraceAsString() . "\n");
        }
        
        // clean up
        
        $state->popTable('oauth_tokens');
    }

    public function testcheckNonce()
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        
        $this->markTestIncomplete("??? Incorrect state hash (in PHPUnitTest): Hash doesn't match at key \"database::oauth_tokens\".");
        
        // save state
        
        $state = new SuiteCRM\StateSaver();
        $state->pushTable('oauth_nonce');
        
        // test
        
        $result = OAuthToken::checkNonce('test', 'test', 123);
        $this->assertEquals(1, $result);
        
        // clean up
        
        $state->popTable('oauth_nonce');
    }

    public function testdeleteByConsumer()
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        
        $this->markTestIncomplete("??? Incorrect state hash (in PHPUnitTest): Hash doesn't match at key \"database::oauth_tokens\".");
        
        //execute the method and test if it works and does not throws an exception.
        try {
            OAuthToken::deleteByConsumer('1');
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->fail("\nException: " . get_class($e) . ": " . $e->getMessage() . "\nin " . $e->getFile() . ':' . $e->getLine() . "\nTrace:\n" . $e->getTraceAsString() . "\n");
        }
    }

    public function testdeleteByUser()
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        
        $this->markTestIncomplete("??? Incorrect state hash (in PHPUnitTest): Hash doesn't match at key \"database::oauth_tokens\".");
        
        //execute the method and test if it works and does not throws an exception.
        try {
            OAuthToken::deleteByUser('1');
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->fail("\nException: " . get_class($e) . ": " . $e->getMessage() . "\nin " . $e->getFile() . ':' . $e->getLine() . "\nTrace:\n" . $e->getTraceAsString() . "\n");
        }
    }

    public function testdisplayDateFromTs()
    {
        $this->markTestIncomplete('Smthing wrong with the oauth_token db table. after this the other tests says: Incorrect state hash (in PHPUnitTest): Hash doesn\'t match at key "database::oauth_tokens".');
        
        $this->markTestIncomplete("??? Incorrect state hash (in PHPUnitTest): Hash doesn't match at key \"database::oauth_tokens\".");
        
        //test with empty array
        $result = displayDateFromTs(array('' => ''), 'timestamp', '');
        $this->assertEquals('', $result);

        //test with a valid array
        $result = displayDateFromTs(array('TIMESTAMP' => '1272508903'), 'timestamp', '');
        $this->assertEquals('04/29/2010 02:41', $result);
    }
}
