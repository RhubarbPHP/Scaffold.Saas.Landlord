<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Scaffolds\Saas\Landlord\Tests\Model\Accounts;

use Rhubarb\RestApi\Exceptions\RestAuthenticationException;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Accounts\Account;
use Rhubarb\Scaffolds\Saas\Landlord\Tests\Fixtures\SaasApiTestCase;

class UserTest extends SaasApiTestCase
{
	public function testAuthentication()
	{
		$me = $this->makeApiCall( "/users/me" );

		$this->assertEquals( "Unit Tester", $me->Forename );
		$this->assertEquals( "unit-tester", $me->Username );
	}

	public function testUserDetailsCanBeChanged()
	{
		$user = new \stdClass();
		$user->Forename = "Billy";

		$result = $this->makeApiCall( "/users/me", "put", $user );

		$this->assertTrue( $result->result->status );

		$result = $this->makeApiCall( "/users/me", "get" );

		$this->assertEquals( "Billy", $user->Forename );

		// Username can't be changed!
		$user->Username = "Bumbler";

		$result = $this->makeApiCall( "/users/me", "put", $user );

		$this->assertFalse( $result->result->status );

		// Unless they're the same!
		$user->Username = "unit-tester";

		$result = $this->makeApiCall( "/users/me", "put", $user );

		$this->assertTrue( $result->result->status );
	}

	public function testPasswordCanBeChanged()
	{
		$user = new \stdClass();
		$user->NewPassword = "newpassword";

		$result = $this->makeApiCall( "/users/me", "put", $user );

		$this->assertTrue( $result->result->status );

		try
		{
			$this->makeApiCall( "/users/me" );
			$this->fail( "Shouldn't be able to authenticate with the wrong password!" );
		}
		catch( RestAuthenticationException $er )
		{

		}

		$this->password = "newpassword";
		$me = $this->makeApiCall( "/users/me" );

		$this->assertEquals( "Unit Tester", $me->Forename );

		$this->password = "abc123";
	}

	public function testAccounts()
	{
		$accounts = $this->makeApiCall( "/users/me/accounts", "get" );

		$this->assertCount( 2, $accounts->items );

		$this->assertEquals( "Widgets Co", $accounts->items[0]->AccountName );
		$this->assertEquals( "Steel Inc.", $accounts->items[1]->AccountName );

		$this->assertEquals( "http:///api/accounts/1", $accounts->items[0]->_href );

		$account = $this->makeApiCall( "/accounts", "post",
		[
			"AccountName" => "New Account"
		]);

		$this->assertEquals( sizeof( Account::Find() ), $account->_id );

		$accounts = $this->makeApiCall( "/users/me/accounts", "get" );

		$this->assertCount( 3, $accounts->items );
	}
} 