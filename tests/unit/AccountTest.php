<?php


use Rhubarb\Scaffolds\Saas\Landlord\Model\Accounts\Account;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\User;
use Rhubarb\Scaffolds\Saas\Landlord\Tests\Fixtures\SaasTestCase;

class AccountTest extends SaasTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testAccountGetsAccountID()
    {
        $account = new Account();
        $account->AccountName = "Grass Mongers";
        $account->save();

        $this->assertEquals("grass-mong", $account->AccountID);

        $account->save();

        $this->assertEquals("grass-mong", $account->AccountID, "The unique reference shouldn't change.");

        $account = new Account();
        $account->AccountName = "Herb Mongers";
        $account->save();

        $this->assertEquals("herb-monge", $account->AccountID);

        $account = new Account();
        $account->AccountName = "Herb Mongers";
        $account->save();

        $this->assertEquals("herb-monge-2", $account->AccountID, "Similar accounts should stil have a unique reference");

        $account = new Account();
        $account->AccountName = "Tra-;Sf=  $";
        $account->save();

        $this->assertEquals("tra-sf", $account->AccountID);
    }

    public function testAccountGetsIV()
    {
        $account = new Account();
        $account->AccountName = "Marys Sweets";
        $account->save();

        $this->assertGreaterThan(0, strlen($account->CredentialsIV), "Accounts should get an IV");

        $oldIv = $account->CredentialsIV;

        $account = new Account();
        $account->AccountName = "Bobs Sweets";
        $account->save();

        $this->assertNotEquals($oldIv, $account->CredentialsIV, "The IV should be different each time");

        $oldIv = $account->CredentialsIV;

        $account->save();

        $this->assertEquals($oldIv, $account->CredentialsIV, "The IV shouldn't change");
    }

    public function testAccountGetsServer()
    {
        $account = new Account();
        $account->AccountName = "Assigned Test";
        $account->save();

        $this->assertNotEquals(0, $account->ServerID, "An account should get assigned a server");

        $firstServer = $account->ServerID;

        $account = new Account();
        $account->AccountName = "Assigned Test";
        $account->save();

        $this->assertNotEquals($firstServer, $account->ServerID, "Given our unit testing data - this account should have gone to a different server.");

        $account = new Account();
        $account->AccountName = "Assigned Test";
        $account->save();

        $this->assertEquals($firstServer, $account->ServerID, "Given our unit testing data - this account should have gone back to the first server.");
    }
}