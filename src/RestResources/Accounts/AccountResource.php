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

namespace Rhubarb\Scaffolds\Saas\Landlord\RestResources\Accounts;

use Rhubarb\Crown\Context;
use Rhubarb\Crown\Logging\Log;
use Rhubarb\Scaffolds\Saas\Landlord\LoginProviders\SaasLoginProvider;
use Rhubarb\RestApi\Resources\ModelRestResource;
use Rhubarb\Stem\Collections\Collection;
use Rhubarb\Stem\Filters\CollectionPropertyMatches;
use string;

class AccountResource extends ModelRestResource
{

    /**
     * Returns the name of the model to use for this resource.
     *
     * @return string
     */
    public function getModelName()
    {
        return "Account";
    }

    protected function getSkeleton()
    {
        $skeleton = parent::getSkeleton();

        if ($this->model) {
            $skeleton->_id = $this->getModel()->AccountID;
        }

        return $skeleton;
    }

    protected function getColumns()
    {
        $columns = parent::getColumns();
        $columns["CredentialsIV"] = "CredentialsIV";
        $columns["Server"] = "Server";

        return $columns;
    }

    public function afterModelCreated($model, $restResource)
    {
        Log::Debug("Account `" . $model->AccountName . "` created", "SaaS");

        // Make sure that new accounts are attached to the authenticated user.
        $login = new SaasLoginProvider();
        $user = $login->getModel();

        $user->Accounts->append($model);
    }

    public function filterModelCollectionForSecurity(Collection $collection)
    {
        $login = new SaasLoginProvider();

        $model = $login->getModel();

        $collection->filter(new CollectionPropertyMatches("UsersRaw", "UserID", $model->UniqueIdentifier));

        parent::filterModelCollectionForSecurity($collection);
    }
}