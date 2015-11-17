<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\RestResources;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\RestApi\Resources\ModelRestResource;
use Rhubarb\Stem\Collections\Collection;
use Rhubarb\Stem\Filters\Equals;

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

    /**
     * Override to filter a model collection to apply any necessary filters only when this is the REST collection of the specific resource being fetched
     *
     * @param Collection $collection
     */
    public function filterModelCollectionAsContainer(Collection $collection)
    {
        parent::filterModelCollectionAsContainer($collection);

        $collection->filter(new Equals("Accepted", false));
    }

    protected function getColumns()
    {
        $columns = parent::getColumns();
        $columns[] = 'UserUUID';
        $columns[] = 'AccountID';
        $columns[] = 'Accepted';
        $columns[] = 'Email';

        return $columns;
    }
}