<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\RestResources;

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
        $columns[] = 'Accepted';

        return $columns;
    }
}