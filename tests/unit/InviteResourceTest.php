<?php


use Rhubarb\Crown\Tests\Fixtures\UnitTestingEmailProvider;
use Rhubarb\Scaffolds\Saas\Landlord\Emails\InviteEmail;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\Invite;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\User;
use Rhubarb\Scaffolds\Saas\Landlord\Tests\Fixtures\SaasApiTestCase;

class InviteResourceTest extends SaasApiTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testListOfInvites()
    {
        $invites = $this->makeApiCall("/accounts/".$this->protonWelding->UniqueIdentifier."/invites");

        $this->assertCount(0, $invites->items, "Proton welding should have no invites.");

        $users = count(User::find());

        $invite = $this->makeApiCall(
            "/accounts/".$this->protonWelding->UniqueIdentifier."/invites",
            'post',
            [ 'Email' => 'some@guy.com' ]
        );

        $this->assertEquals( $this->protonWelding->UniqueIdentifier, $invite->AccountID );

        $this->assertObjectHasAttribute('_id', $invite);
        $this->assertGreaterThan( 24, strlen( $invite->_id ) );

        $this->assertEquals( Invite::findLast()->UniqueIdentifier, $invite->_id );
        $this->assertCount($users+1, User::find(), "A user should have been created");
        $this->assertEquals( 'some@guy.com', $this->findLastUser()->Email, "Invited user's should match invite email" );
        $this->assertEquals( $this->findLastUser()->UUID, $invite->UserUUID, "No User UUID in invite response" );

        $lastInviteEmail = UnitTestingEmailProvider::GetLastEmail();

        $this->assertInstanceOf( InviteEmail::class, $lastInviteEmail);
        $this->assertEquals( $invite->_id, $lastInviteEmail->getInvite()->UniqueIdentifier );
    }

    public function testNewInviteForExistingUser()
    {
        $user = new User();
        $user->Username = "billybob";
        $user->Forename = "Billy";
        $user->Email = "billybob@abc123.com";
        $user->NewPassword = "abc123";
        $user->save();

        $invite = $this->makeApiCall(
            "/accounts/".$this->protonWelding->UniqueIdentifier."/invites",
            'post',
            [ 'Email' => 'billybob@abc123.com' ]
        );

        $this->assertEquals($user->UUID, $invite->UserUUID, "A new user should not have been created");
    }

    /**
     * @return \Rhubarb\Stem\Models\Model|static
     * @throws \Rhubarb\Stem\Exceptions\RecordNotFoundException
     */
    public function findLastUser()
    {
        return User::findLast();
    }

}