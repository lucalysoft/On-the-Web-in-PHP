<?php

class BasicModuleCest
{
    /**
     * @param AcceptanceTester $I
     */
    public function _before(AcceptanceTester $I)
    {
    }

    /**
     * @param AcceptanceTester $I
     */
    public function _after(AcceptanceTester $I)
    {
    }

    public function createBasicModule(\Step\Acceptance\Administration $I, \Helper\WebDriverHelper $webDriverHelper)
    {

        $I->wantTo('Create a basic module for testing');
        $I->amOnUrl(
            $webDriverHelper->getInstanceURL()
        );

        $I->loginAsAdmin($webDriverHelper);
        $I->gotoAdministration();

        // Go To Module Builder
        $I->click('#moduleBuilder');

        // Create new package
        $I->click('#newPackageLink');
        $I->wait(3);
        $I->fillField(['name' => 'name'], \Page\BasicModule::$NAME);
        $I->fillField(['name' => 'author'], 'Acceptance Tester');
        $I->fillField(['name' => 'key'], 'Test');
        $I->fillField(['name' => 'description'], 'test module');
        $I->click('Save');

        // Close confirmation window
        $I->wait(3);
        $I->click('.container-close');

        // Create new basic module
        $I->click('New Module');
        $I->waitForElement('[name="label"]');
        $I->fillField(['name' => 'name'], \Page\BasicModule::$NAME);
        $I->fillField(['name' => 'label'], \Page\BasicModule::$NAME);
        $I->checkOption('[name=importable]');
        $I->click('#type_basic');
        $I->click('Save');

        // Close popup
        $I->wait(3);
        $I->click('.container-close');

        // Deploy module
        $I->wait(3);
        $I->click('Module Builder');
        $I->wait(3);
        $I->click(\Page\BasicModule::$NAME, '.bodywrapper');
        $I->wait(3);
        $I->click('Deploy');

        // Close popup
        $I->wait(3);
        $I->click('.container-close');

        // Wait for page to refresh and look for new package link
        $I->waitForElement('#newPackageLink', 120);
    }

    // tests
}