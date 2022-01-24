<?php
namespace Step\Acceptance;

class User extends \AcceptanceTester
{

    public function amLogin()
    {
        $I = $this;
        $I->amOnPage('/login');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'admin123');
        $I->click('Sign in');

        return $this;
    }

}
