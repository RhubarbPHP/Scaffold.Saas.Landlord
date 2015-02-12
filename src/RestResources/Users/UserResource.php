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
use Rhubarb\RestApi\Resources\ModelRestResource;

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

    protected function getColumns()
    {
        return ["Username", "Forename", "Surname", "Email", "Enabled"];
    }

    protected function beforeModelUpdated($model, $restResource)
    {
        if (isset($restResource["NewPassword"])) {
            Log::Debug("User `" . $model->RealName . "` (`" . $model->UniqueIdentifier . "`) password changed.",
                "SaaS");

            $model->setNewPassword($restResource["NewPassword"]);
        }
    }
}