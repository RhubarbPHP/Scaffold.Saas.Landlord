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

namespace Rhubarb\Scaffolds\Saas\Landlord\Model\Users;

use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Schema\Columns\Boolean;
use Rhubarb\Stem\Schema\ModelSchema;

class User extends \Rhubarb\Scaffolds\AuthenticationWithRoles\User
{
    protected function extendSchema(ModelSchema $schema)
    {
        $schema->addColumn(new Boolean("LandlordUser", false));

        parent::extendSchema($schema);
    }

    public static function findTenantUsers()
    {
        return self::find(new Equals("LandlordUser", false));
    }

    public static function findLandlordUsers()
    {
        return self::find(new Equals("LandlordUser", true));
    }
} 