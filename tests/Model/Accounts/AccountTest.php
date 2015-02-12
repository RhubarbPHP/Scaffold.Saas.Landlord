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

namespace Rhubarb\Crown\Scaffolds\Saas\Model\Accounts;

use Rhubarb\Scaffolds\AuthenticationWithRoles\User;
use Rhubarb\Scaffolds\Saas\Landlord\Tests\Fixtures\SaasTestCase;

class AccountTest extends SaasTestCase
{
	public function testInvites()
	{
		$user = new User();
		$user->Username = "aasdfa";
		$user->Forename = "Mary";
		$user->SetNewPassword( "asdf" );
		$user->Save();

		$this->steelInc->Invite( $user );

		$this->assertCount( 1, $user->Invites );
		$this->assertEquals( $this->steelInc->UniqueIdentifier, $user->Invites[0]->AccountID );
		// Check the expiry date on the invite.
		//$this->assertEquals( )
	}
}
 