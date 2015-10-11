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
use Rhubarb\RestApi\Resources\ModelRestResource;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Accounts\AccountUser;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\Invite;
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

    public function filterModelCollectionAsContainer(Collection $collection)
    {
        if ( $this->parentResource instanceof AccountResource ){
            $collection->filter(new CollectionPropertyMatches("AccountsRaw", "AccountID", $this->parentResource->getModel()->UniqueIdentifier ));
        }

        parent::filterModelCollectionAsContainer($collection); // TODO: Change the autogenerated stub
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
        if( isset( $restResource[ 'InviteUUID' ] ) )
        {
            $invite = new Invite( $restResource[ 'InviteUUID' ] );
            $account = $invite->Account;
            $user = $invite->User;
            try{
                AccountUser::findFirst(new AndGroup([
                    new Equals('AccountID', $account->AccountID),
                    new Equals('UserID', $user->UserID)
                ]));
            }
            catch( RecordNotFoundException $ex )
            {
                $accountUser = new AccountUser();
                $accountUser->AccountID = $account->AccountID;
                $accountUser->UserID = $user->UserID;
                $accountUser->save();
            }

            $this->setModel( $user );
            $this->put($restResource);
            return $this->get();
        }
        else
        {
            return parent::post($restResource);
        }
    }
}