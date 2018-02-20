<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Scaffolds\Saas\Landlord\RestResources\Users;

use Rhubarb\Crown\Logging\Log;
use Rhubarb\RestApi\Exceptions\RestRequestPayloadValidationException;
use Rhubarb\RestApi\Exceptions\RestResourceNotFoundException;
use Rhubarb\RestApi\Resources\ModelRestResource;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Accounts\AccountUser;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\Invite;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\User;
use Rhubarb\Scaffolds\Saas\Landlord\RestResources\Accounts\AccountResource;
use Rhubarb\Stem\Collections\Collection;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;
use Rhubarb\Stem\Filters\AndGroup;
use Rhubarb\Stem\Filters\CollectionPropertyMatches;
use Rhubarb\Stem\Filters\Equals;

class UserResource extends ModelRestResource
{
    /**
     * Returns the name of the model to use for this resource.
     *
     * @return string
     */
    public function getModelName()
    {
        return "User";
    }

    protected function getSkeleton()
    {
        $skeleton = parent::getSkeleton();

        if ( $this->model ) {
            $skeleton->_id = $this->getModel()->UUID;
        }

        return $skeleton;
    }

    /**
     * Returns the ItemRestResource for the $resourceIdentifier contained in this collection.
     *
     * @param $resourceIdentifier
     * @return ItemRestResource
     * @throws RestImplementationException Thrown if the item could not be found.
     */
    public function createItemResource($resourceIdentifier)
    {
        try {
            $model = User::findFirst(new Equals("UUID", $resourceIdentifier));
        } catch (RecordNotFoundException $er) {
            throw new RestResourceNotFoundException(self::class, $resourceIdentifier);
        }

        return $this->getItemResourceForModel($model);
    }


    protected function getColumns()
    {
        return [
            "UUID",
            "Username",
            "Forename",
            "Surname",
            "Email",
            "Enabled"
        ];
    }

    public function validateRequestPayload($payload, $method)
    {
        if ($method == "post") {
            if (!isset($payload["NewPassword"]) || $payload["NewPassword"] == "") {
                throw new RestRequestPayloadValidationException("New users must have a password");
            }
        }

        parent::validateRequestPayload($payload, $method);
    }

    protected function createModelCollection()
    {
        return User::find(new Equals("Enabled", true));
    }


    public function filterModelCollectionAsContainer(Collection $collection)
    {
        if ( $this->parentResource instanceof AccountResource ){
            $collection->filter(new CollectionPropertyMatches("AccountsRaw", "AccountID", $this->parentResource->getModel()->UniqueIdentifier ));
        }

        parent::filterModelCollectionAsContainer($collection);
    }


    protected function beforeModelUpdated($model, $restResource)
    {
        if (isset($restResource["NewPassword"])) {
            Log::debug("User `" . $model->FullName . "` (`" . $model->UniqueIdentifier . "`) password changed.", "SaaS");

            $model->setNewPassword($restResource["NewPassword"]);
        }
    }

    public function post($restResource)
    {
        if(isset($restResource['InviteID']))
        {
            $invite = new Invite( $restResource[ 'InviteID' ] );
            $this->setModel( $invite->User );
            $this->put($restResource);

            $invite->accept();

            return $this->get();
        }
        else
        {
            return parent::post($restResource);
        }
    }
}