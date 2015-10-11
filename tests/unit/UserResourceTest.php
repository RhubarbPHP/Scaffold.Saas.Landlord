<?php

use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\Invite;
use Rhubarb\Scaffolds\Saas\Landlord\Tests\Fixtures\SaasApiTestCase;
use RightRevenue\Landlord\Models\Users\User;

class UserResourceTest extends SaasApiTestCase
{
    public function testWhenUserSignsUpFromInvite()
    {
        $invite = new Invite();
        $invite->Email = "nobody@goatlovers.com";
        $invite->AccountID = $this->steelInc->UniqueIdentifier;
        $invite->save();

        $r = $this->makeApiCall( "/users", "post",[
            "Username" => "bbb",
            "Forename" => "aaa",
            "NewPassword" => "ccc",
            "Email" => "wer@wer.com",
            "InviteUUID" => $invite->UniqueIdentifier
        ]);

        $user = User::findLast();

        $this->assertEquals( $invite->UserID, $user->UserID );

        $this->assertEquals("wer@wer.com", $user->Email );

        $this->assertEquals($user->Accounts[0]->UniqueIdentifier, $this->steelInc->UniqueIdentifier, "The user wasn't auto attached to an account upon sign up" );
    }
}