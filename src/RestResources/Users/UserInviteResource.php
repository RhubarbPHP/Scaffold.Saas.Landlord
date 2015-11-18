<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\RestResources\Users;

use Rhubarb\Crown\Context;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\Invite;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\User;
use Rhubarb\Scaffolds\Saas\Landlord\RestResources\InviteResource;
use Rhubarb\Stem\Collections\Collection;
use Rhubarb\Stem\Filters\Equals;

class UserInviteResource extends InviteResource
{
    public function filterModelCollectionForSecurity(Collection $collection)
    {
        parent::filterModelCollectionForSecurity($collection);

        $user = $this->getUser();

        // See if an invitation code is being redeemed.
        $invitationCode = Context::currentRequest()->get("invitation");

        if ($invitationCode){
            $invitation = new Invite($invitationCode);
            $invitation->UserID = $user->UserID;

            $account = $invitation->Account;

            if ($account->Users->containsUniqueIdentifier($user->UserID)){
                // Accept the invitation if the user already has access to the account.
                $invitation->accept();
            } else {
                // Otherwise save the invitation just with the new UserID
                $invitation->save();
            }

        }

        $collection->filter( new Equals( 'UserID', $user->UserID ) );
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        return $this->parentResource->getModel();
    }
}