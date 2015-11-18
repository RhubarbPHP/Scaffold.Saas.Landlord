<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\RestResources\Accounts;

use Rhubarb\Crown\Exceptions\ImplementationException;
use Rhubarb\RestApi\Exceptions\RestImplementationException;
use Rhubarb\RestApi\Resources\ModelRestResource;
use Rhubarb\Scaffolds\Saas\Landlord\RestResources\Accounts\AccountResource;
use Rhubarb\Scaffolds\Saas\Landlord\RestResources\InviteResource;
use Rhubarb\Stem\Collections\Collection;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;
use Rhubarb\Stem\Filters\Equals;

class AccountInviteResource extends InviteResource
{

    public function post($restResource)
    {
        $restResource['AccountID'] = $this->parentResource->getModel()->UniqueIdentifier;

        return parent::post($restResource);
    }

}