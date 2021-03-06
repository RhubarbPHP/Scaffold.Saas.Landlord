<?php


use Rhubarb\Crown\Email\EmailProvider;
use Rhubarb\Crown\Email\SimpleEmail;
use Rhubarb\Crown\Tests\Fixtures\UnitTestingEmailProvider;
use Rhubarb\Scaffolds\Saas\Landlord\Emails\InviteEmail;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Accounts\AccountUser;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\Invite;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\User;
use Rhubarb\Scaffolds\Saas\Landlord\Tests\Fixtures\SaasApiTestCase;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;
use Rhubarb\Stem\Filters\AndGroup;
use Rhubarb\Stem\Filters\Equals;

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

        /** @var InviteEmail $lastInviteEmail */
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

        $countOfInvites = count(Invite::find());

        $invite = $this->makeApiCall(
            "/accounts/".$this->protonWelding->UniqueIdentifier."/invites",
            'post',
            [ 'Email' => 'billybob@abc123.com' ]
        );

        $this->assertEquals($user->UUID, $invite->UserUUID, "A new user should not have been created");
        $this->assertCount($countOfInvites + 1, Invite::find(), "An invitation should have been created");
    }

    public function testDoubleInvite()
    {
        $this->makeApiCall(
            "/accounts/".$this->protonWelding->UniqueIdentifier."/invites",
            'post',
            [ 'Email' => 'billybob@abc123.com' ]
        );

        $lastEmail = UnitTestingEmailProvider::GetLastEmail();

        $this->makeApiCall(
            "/accounts/".$this->protonWelding->UniqueIdentifier."/invites",
            'post',
            [ 'Email' => 'billybob@abc123.com' ]
        );

        $newEmail = UnitTestingEmailProvider::GetLastEmail();

        $this->assertNotSame($lastEmail, $newEmail, "An invitation should have been sent however.");
    }

    public function testNewInviteForExistingUserAlreadyInTheAccount()
    {
        $user = new User();
        $user->Username = "billybob";
        $user->Forename = "Billy";
        $user->Email = "billybob@abc123.com";
        $user->NewPassword = "abc123";
        $user->save();

        $countOfInvites = count(Invite::find());

        $this->protonWelding->attachUser($user);

        $invite = $this->makeApiCall(
            "/accounts/".$this->protonWelding->UniqueIdentifier."/invites",
            'post',
            [ 'Email' => 'billybob@abc123.com' ]
        );

        $this->assertCount($countOfInvites, Invite::find(), "An invitation should have been created");
    }


    /**
     * @return \Rhubarb\Stem\Models\Model|static
     * @throws RecordNotFoundException
     */
    public function findLastUser()
    {
        return User::findLast();
    }

    public function testAcceptInvite()
    {
        $invite = new Invite();
        $invite->UserID = $this->nigel->getUniqueIdentifier();
        $invite->AccountID = $this->steelInc->getUniqueIdentifier();
        $invite->save();

        $originalUsername = $this->username;
        $this->username = $this->nigel->Username;

        // list this user's invites
        $response = $this->MakeApiCall( "/users/me/invites/" );
        $this->assertCount( 1, $response->items, 'must be able to list invites for this user.' );
        $responseItem = $response->items[0];

        $this->assertEquals($invite->UniqueIdentifier, $responseItem->_id, "Invite id should match");
        $this->assertEquals($invite->AccountID, $responseItem->AccountID, 'Response invite should include account ID');

        // Check that the last email
        /** @var InviteEmail $firstInviteEmail */
        $firstInvited = UnitTestingEmailProvider::GetLastEmail()->getRecipientList();

        // Send another email
        $email = new SimpleEmail();
        $email->addRecipient( 'some@not.invite' );
        $email->send();

        // Accept the invite
        $updateResponse = $this->MakeApiCall( "/users/me/invites/" . $invite->getUniqueIdentifier(), 'put', ['Accepted' => true] );

        $this->assertTrue($updateResponse->result->status, 'Failed to PUT new accepted flag for invite');

        $this->assertNotEquals($firstInvited, UnitTestingEmailProvider::GetLastEmail()->getRecipientList(), 'A second invite email should not be sent.');

        try{
            $accountUser = AccountUser::FindFirst( new AndGroup([
                new Equals('AccountID', $invite->AccountID),
                new Equals('UserID', $invite->UserID)
            ]) );
        }
        catch( RecordNotFoundException $ex )
        {
            $accountUser = null;
        }
        $this->assertNotNull( $accountUser, 'No account-user link created for accepted invite' );

        $this->username = $originalUsername;
    }
}