<?php

use Step\Acceptance\User;

class BasicCest
{
    public function setup(AcceptanceTester $I)
    {
        // remove versions
        !file_exists($file = COSILER_TMP . '/version.txt') || unlink($file);

        $I->amOnPage('/setup');
        $I->see('Run application setup');
        $I->see('Current environment: test');
        $I->click('RUN SETUP NOW');
        $I->seeCurrentUrlEquals('/');
    }

    /**
     * @depends setup
     */
    public function setupAgain(AcceptanceTester $I)
    {
        $I->amOnPage('/setup');
        $I->see('[404] Not Found');
        $I->see('The requested page does not exists');
        $I->click('Home');
        $I->seeCurrentUrlEquals('/');
    }

    /**
     * @depends setup
     */
    public function visit(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->see('Welcome to My Applications');
        $I->dontSee('You are logged in as Administrator.');
        $I->click('login');
        $I->seeCurrentUrlEquals('/login');
    }

    /**
     * @depends visit
     */
    public function login(User $I)
    {
        $I->amLogin();
        $I->seeCurrentUrlEquals('/');
        $I->see('Welcome to My Applications');
        $I->see('You are logged in as Administrator.');
    }

    /**
     * @depends login
     */
    public function loginAgain(User $I)
    {
        $I->amLogin();
        $I->amOnPage('/login');
        $I->seeCurrentUrlEquals('/');
        $I->see('Welcome to My Applications');
        $I->see('You are logged in as Administrator.');
    }

    /**
     * @depends login
     */
    public function logout(User $I)
    {
        $I->amLogin();
        $I->seeCurrentUrlEquals('/');
        $I->see('Welcome to My Applications');
        $I->see('You are logged in as Administrator.');
        $I->click('LOGOUT');
        $I->seeCurrentUrlEquals('/');
        $I->dontSee('You are logged in as Administrator.');
    }

    /**
     * @depends visit
     */
    public function logoutAnonymous(AcceptanceTester $I)
    {
        $I->amOnPage('/logout');
        $I->seeCurrentUrlEquals('/');
        $I->see('Welcome to My Applications');
        $I->dontSee('You are logged in as Administrator.');
    }

    public function unknownPage(AcceptanceTester $I)
    {
        $I->amOnPage('/unknown');
        $I->see('[404] Not Found');
        $I->see('The requested page does not exists');
    }
}
