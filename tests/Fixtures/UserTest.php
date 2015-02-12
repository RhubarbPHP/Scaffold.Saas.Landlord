<?php

namespace Gcd\Core\Scaffolds\Saas\UnitTesting;

use Gcd\Core\RestApi\Exceptions\RestAuthenticationException;
use Gcd\Core\Scaffolds\Saas\Model\Accounts\Account;

class UserTest extends SaasApiTestCase
{
	public function testAuthentication()
	{
		$me = $this->MakeApiCall( "/users/me" );

		$this->assertEquals( "Unit Tester", $me->Forename );
		$this->assertEquals( "unit-tester", $me->Username );
	}

	public function testUserDetailsCanBeChanged()
	{
		$user = new \stdClass();
		$user->Forename = "Billy";

		$result = $this->MakeApiCall( "/users/me", "put", $user );

		$this->assertTrue( $result->result->status );

		$result = $this->MakeApiCall( "/users/me", "get" );

		$this->assertEquals( "Billy", $user->Forename );

		// Username can't be changed!
		$user->Username = "Bumbler";

		$result = $this->MakeApiCall( "/users/me", "put", $user );

		$this->assertFalse( $result->result->status );

		// Unless they're the same!
		$user->Username = "unit-tester";

		$result = $this->MakeApiCall( "/users/me", "put", $user );

		$this->assertTrue( $result->result->status );
	}

	public function testPasswordCanBeChanged()
	{
		$user = new \stdClass();
		$user->NewPassword = "newpassword";

		$result = $this->MakeApiCall( "/users/me", "put", $user );

		$this->assertTrue( $result->result->status );

		try
		{
			$this->MakeApiCall( "/users/me" );
			$this->fail( "Shouldn't be able to authenticate with the wrong password!" );
		}
		catch( RestAuthenticationException $er )
		{

		}

		$this->_password = "newpassword";
		$me = $this->MakeApiCall( "/users/me" );

		$this->assertEquals( "Unit Tester", $me->Forename );

		$this->_password = "abc123";
	}

	public function testAccounts()
	{
		$accounts = $this->MakeApiCall( "/users/me/accounts", "get" );

		$this->assertCount( 2, $accounts->items );

		$this->assertEquals( "Widgets Co", $accounts->items[0]->AccountName );
		$this->assertEquals( "Steel Inc.", $accounts->items[1]->AccountName );

		$this->assertEquals( "http:///api/accounts/1", $accounts->items[0]->_href );

		$account = $this->MakeApiCall( "/accounts", "post",
		[
			"AccountName" => "New Account"
		]);

		$this->assertEquals( sizeof( Account::Find() ), $account->_id );

		$accounts = $this->MakeApiCall( "/users/me/accounts", "get" );

		$this->assertCount( 3, $accounts->items );
	}
} 