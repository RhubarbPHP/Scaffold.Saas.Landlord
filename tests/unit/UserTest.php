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

namespace Rhubarb\Scaffolds\Saas\Landlord\Tests\Model\Users;

use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\User;
use Rhubarb\Scaffolds\Saas\Landlord\Tests\Fixtures\SaasTestCase;
use Rhubarb\Stem\Filters\Equals;

class UserTest extends SaasTestCase
{
    public function testTenantUsersDoestIncludeLandlordUsers()
    {
        $users = User::findTenantUsers();
        $users->filter(new Equals("Username", "admin"));

        $this->assertCount(0, $users, "The count of tenants with username admin should be zero.");

        $users = User::findLandlordUsers();
        $users->filter(new Equals("Username", "nigel"));

        $this->assertCount(0, $users, "The count of landlord users with username nigel should be zero.");
    }
}
 