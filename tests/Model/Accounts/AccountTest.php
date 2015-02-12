<?php

namespace Gcd\Core\Scaffolds\Saas\Model\Accounts;

use Gcd\Core\Scaffolds\AuthenticationWithRoles\User;
use Gcd\Core\Scaffolds\Saas\UnitTesting\SaasTestCase;

class AccountTest extends SaasTestCase
{
	public function testInvites()
	{
		$user = new User();
		$user->Username = "aasdfa";
		$user->Forename = "Mary";
		$user->SetNewPassword( "asdf" );
		$user->Save();

		$this->_steelInc->Invite( $user );

		$this->assertCount( 1, $user->Invites );
		$this->assertEquals( $this->_steelInc->UniqueIdentifier, $user->Invites[0]->AccountID );
		// Check the expiry date on the invite.
		//$this->assertEquals( )
	}
}
 