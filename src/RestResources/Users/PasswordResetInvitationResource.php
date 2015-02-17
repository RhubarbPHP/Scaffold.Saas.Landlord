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
use Rhubarb\RestApi\UrlHandlers\RestHandler;
use Rhubarb\Scaffolds\AuthenticationWithRoles\User;

/**
 * Handles password reset invitations and resources
 *
 * Note this is covered by unit tests in the core.saas.tenant library.
 */
class PasswordResetInvitationResource extends RestResource
{
    public function put($restResource, RestHandler $handler = null)
    {
        $hash = $restResource["PasswordResetHash"];
        $newPassword = $restResource["NewPassword"];

        $user = User::fromPasswordResetHash($hash);
        $user->setNewPassword($newPassword);
        $user->save();
    }

    public function post($restResource, RestHandler $handler = null)
    {
        $username = $restResource["Username"];

        $user = User::fromUsername($username);
        $hash = $user->generatePasswordResetHash();

        $response = new \stdClass();
        $response->PasswordResetHash = $hash;
        $response->Email = $user->Email;
        $response->FullName = $user->FullName;

        return $response;
    }
} 