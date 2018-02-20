<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\RestResources\Accounts;

use Rhubarb\Scaffolds\Saas\Landlord\RestResources\InviteResource;

class AccountInviteRevokeResource extends InviteResource
{

    public function put($restResource)
    {
        return $this->parentResource->put($restResource);
    }
}
