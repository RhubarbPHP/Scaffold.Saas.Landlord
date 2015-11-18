<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\Tests\Model\Users;

use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\Invite;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\User;
use Rhubarb\Scaffolds\Saas\Landlord\Tests\Fixtures\SaasApiTestCase;

class UserInviteResourceTest extends SaasApiTestCase
{
    private $user;

    /**
     * @Override
     */
    protected function _before()
    {
        parent::_before();

        $this->user = new User();
        $this->user->Forename = "John";
        $this->user->Username = "jdoe";
        $this->user->NewPassword = "jdoe";
        $this->user->Email = "billyjean@gmail.com";
        $this->user->save();

        $this->username = "jdoe";
        $this->password = "jdoe";
    }

    public function testInvitationCodeCanBeRedeemedToAnExistingUser()
    {
        $invitation = new Invite();
        $invitation->AccountID = $this->protonWelding->UniqueIdentifier;
        $invitation->Email = "billybob@gmail.com";
        $invitation->save();

        $response = $this->makeApiCall( "/users/me/invites" );

        $this->assertCount(0, $response->items, "The user should start with no invites" );

        // Simulate query params.
        $_GET["invitation"] = $invitation->InviteID;

        $response = $this->makeApiCall( "/users/me/invites" );

        $this->assertCount(1, $response->items, "The user should now have an invite" );
    }

    public function testInvitationCodeIsSquashedIfRedeemedToAnUserWithAccessAlready()
    {
        $this->protonWelding->attachUser($this->user);

        $invitation = new Invite();
        $invitation->AccountID = $this->protonWelding->AccountID;
        $invitation->Email = "billybob@gmail.com";
        $invitation->save();

        // Simulate query params.
        $_GET["invitation"] = $invitation->InviteID;

        $response = $this->makeApiCall( "/users/me/invites" );

        $this->assertCount(0, $response->items, "The user should have no invites as they've already got access" );

        $invitation->reload();
        $this->assertTrue($invitation->Accepted, "The invitation should have been accepted");
    }
}