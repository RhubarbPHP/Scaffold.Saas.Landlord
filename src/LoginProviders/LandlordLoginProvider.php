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

namespace Rhubarb\Scaffolds\Saas\Landlord\LoginProviders;

use Rhubarb\Crown\LoginProviders\Exceptions\LoginFailedException;
use Rhubarb\Scaffolds\Authentication\LoginProviders\LoginProvider;

/**
 * The login provider to control authentication of users to the landlord admin system itself.
 *
 */
class LandlordLoginProvider extends LoginProvider
{
    protected function checkUserIsPermitted($user)
    {
        parent::checkUserIsPermitted($user);

        if (!$user->LandlordUser) {
            throw new LoginFailedException();
        }
    }
}