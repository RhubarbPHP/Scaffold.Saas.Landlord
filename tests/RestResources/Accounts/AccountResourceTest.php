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

namespace Rhubarb\Scaffolds\Saas\Landlord\Tests\RestResources\Accounts;

use Rhubarb\Scaffolds\Saas\Landlord\Tests\Fixtures\SaasApiTestCase;

class AccountResourceTest extends SaasApiTestCase
{
    protected function getUsername()
    {
        return "nigel";
    }

    protected function getPassword()
    {
        return "abc123";
    }

    public function testAccountsOnlyReturnsUsersAccounts()
    {
        $accounts = $this->makeApiCall("/accounts");

        $this->assertEquals(1, $accounts->count);
        $this->assertEquals("Proton Welding", $accounts->items[0]->AccountName);

        $accounts = $this->makeApiCall("/accounts/234");

        $this->assertFalse($accounts->result->status, "You shouldn't be able to access this account!");
    }
}
 