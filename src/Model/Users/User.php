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

use Rhubarb\Stem\Collections\Collection;
use Rhubarb\Stem\Filters\AndGroup;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Filters\Filter;
use Rhubarb\Stem\Schema\Columns\Boolean;
use Rhubarb\Stem\Schema\ModelSchema;

class User extends \Rhubarb\Scaffolds\AuthenticationWithRoles\User
{
    protected function extendSchema(ModelSchema $schema)
    {
        $schema->addColumn(new Boolean("LandlordUser", false));

        parent::extendSchema($schema);
    }

    /**
     * @param Filter|null $filter
     *
     * @return Collection|static[]
     */
    public static function findTenantUsers($filter = null)
    {
        return self::findUsersWithLandlordUserFlag($filter, false);
    }

    /**
     * @param Filter|null $filter
     *
     * @return Collection|static[]
     */
    public static function findLandlordUsers($filter = null)
    {
        return self::findUsersWithLandlordUserFlag($filter, true);
    }

    /**
     * @param Filter|null $filter
     * @param bool        $landlordUserFlag
     *
     * @return Collection|static[]
     */
    private static function findUsersWithLandlordUserFlag($filter, $landlordUserFlag)
    {
        $landlordUserFilter = new Equals('LandlordUser', $landlordUserFlag);
        if ($filter === null) {
            $filter = $landlordUserFilter;
        } else {
            $filter = new AndGroup([$filter, $landlordUserFilter]);
        }

        return self::find($filter);
    }
} 