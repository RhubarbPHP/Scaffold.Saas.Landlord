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

use Rhubarb\RestApi\Resources\RestResource;
use Rhubarb\Scaffolds\Saas\Landlord\SaasLandlordModule;
use Rhubarb\Stem\Collections\Collection;
use Rhubarb\Stem\Filters\Equals;

class MeResource extends UserResource
{

    public function __construct(RestResource $parentResource = null)
    {
        parent::__construct($parentResource);

        // If the user is authenticated we can simply get the logged in model. Otherwise this
        // will throw an exception.
        $login = SaasLandlordModule::getTenantLoginProvider();
        $this->model = $login->getModel();

        $this->_id = $this->model->getUniqueIdentifier();
    }

    public function filterModelCollectionForSecurity(Collection $collection)
    {
        parent::filterModelCollectionForSecurity();
        $collection->filter( new Equals($this->model->UniqueIdentifierColumnName, $this->model->UniqueIdentifier) );
    }

}