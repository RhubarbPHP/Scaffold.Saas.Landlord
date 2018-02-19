<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\RestResources\Accounts;

use Rhubarb\Scaffolds\Saas\Landlord\RestResources\InviteResource;

class AccountInviteRevokeResource extends InviteResource
{

    public function put($restResource)
    {
        $restResource['Revoked'] = true;

        return parent::put($restResource);
    }
}
