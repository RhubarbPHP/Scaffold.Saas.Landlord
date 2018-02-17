<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\RestResources\Accounts;

use Rhubarb\Scaffolds\Saas\Landlord\RestResources\InviteResource;

class AccountInviteRevokeResource extends InviteResource
{

    public function post($restResource)
    {
        $restResource['AccountID'] = $this->parentResource->getModel()->UniqueIdentifier;
        $restResource['Revoked'] = true;

        return parent::post($restResource);
    }
}
