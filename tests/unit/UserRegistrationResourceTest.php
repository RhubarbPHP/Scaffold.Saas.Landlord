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

namespace Rhubarb\Scaffolds\Saas\Landlord\Tests\RestResources\Users;

use Rhubarb\Scaffolds\Saas\Landlord\Tests\Fixtures\SaasApiTestCase;
use Rhubarb\Scaffolds\AuthenticationWithRoles\User;
use Rhubarb\Stem\Filters\Equals;

class UserRegistrationResourceTest extends SaasApiTestCase
{
    public function getUsername()
    {
        // Forcibly disable authentication
        return "";
    }

    public function getPassword()
    {
        // Forcibly disable authentication
        return "";
    }

    protected function GetToken()
    {
        return "faketoken";
    }

    public function testPublicCanRegister()
    {
        // Test you must supply key details
        $result = $this->makeApiCall("/users", "post", [
            "Username" => ""
        ]);

        $this->assertFalse($result->result->status);

        $result = $this->makeApiCall("/users", "post", [
            "Username" => "abc123",
            "Forename" => ""
        ]);

        $this->assertFalse($result->result->status);

        $result = $this->makeApiCall("/users", "post", [
            "Username" => "abc123",
            "Forename" => "Andrew"
        ]);

        $this->assertFalse($result->result->status, "You should need to supply a password");

        $result = $this->makeApiCall("/users", "post", [
            "Username" => "abc123",
            "Forename" => "test",
            "NewPassword" => "abc",
        ]);

        $this->assertEquals("test", $result->Forename);

        $result = $this->makeApiCall("/users", "post", [
            "Username" => "nancy",
            "NewPassword" => "bell",
            "Email" => "jbloggs@hotmail.com",
            "Forename" => "Nancy",
            "Surname" => "Bell"
        ]);

        $this->assertEquals($result->Email, User::FindFirst(new Equals("Username", "nancy"))->Email);

        // Test you can't update users through this call.

        $result = $this->makeApiCall("/users/" . $result->_id, "put", $result);

        $this->assertFalse($result->result->status);

        // Test you can't create a duplicate on username.
        $result = $this->makeApiCall("/users", "post", [
            "Username" => "nancy",
            "NewPassword" => "bell",
            "Email" => "jbloggs@hotmail.com",
            "Forename" => "Nancy",
            "Surname" => "Bell"
        ]);

        $this->assertFalse($result->result->status);
    }
}