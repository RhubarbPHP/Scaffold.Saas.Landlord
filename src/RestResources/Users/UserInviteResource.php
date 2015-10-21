<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\RestResources\Users;

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