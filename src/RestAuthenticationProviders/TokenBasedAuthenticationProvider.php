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

namespace Rhubarb\Scaffolds\Saas\Landlord\RestAuthenticationProviders;

use Rhubarb\Scaffolds\Saas\Landlord\LoginProviders\SaasLoginProvider;
use Rhubarb\Scaffolds\TokenBasedRestApi;
use Rhubarb\Stem\Models\Model;

class TokenBasedAuthenticationProvider extends TokenBasedRestApi\Authentication\TokenAuthenticationProvider
{
    protected function logUserIn(Model $user)
    {
        $loginProvider = new SaasLoginProvider();
        $loginProvider->forceLogin($user);
    }
}