<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\RestResources\Users;

use Rhubarb\RestApi\Resources\ModelRestResource;

class InviteResource extends ModelRestResource
{
    /**
     * Returns the name of the model to use for this resource.
     *
     * @return string
     */
    public function getModelName()
    {
        return "Invite";
    }

    protected function getColumns()
    {
        $columns = parent::getColumns();
        $columns[] = 'UserUUID';
        $columns[] = 'AccountID';

        return $columns;
    }

    public function post($restResource)
    {
        $restResource['AccountID'] = $this->parentResource->getModel()->UniqueIdentifier;
        return parent::post($restResource);
    }


}