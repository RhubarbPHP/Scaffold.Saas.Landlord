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

use Rhubarb\Crown\DependencyInjection\Container;
use Rhubarb\Crown\Exceptions\EmailException;
use Rhubarb\Crown\Sendables\Email\Email;
use Rhubarb\Crown\Sendables\Email\EmailProvider;
use Rhubarb\Crown\Sendables\Sendable;
use Rhubarb\RestApi\Resources\RestResource;
use Rhubarb\RestApi\UrlHandlers\RestHandler;
use Rhubarb\Scaffolds\Authentication\Emails\ResetPasswordInvitationEmail;
use Rhubarb\Scaffolds\Authentication\LoginProviders\LoginProvider;
use Rhubarb\Scaffolds\Authentication\Settings\AuthenticationSettings;
use Rhubarb\Scaffolds\AuthenticationWithRoles\User;
use Rhubarb\Scaffolds\Saas\Landlord\SaasLandlordModule;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\LoginProviders\ModelLoginProvider;

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

        try {
            $user = User::fromPasswordResetHash($hash);
            $user->setNewPassword($newPassword);
            $user->save();
        }
        catch (RecordNotFoundException $er){
            return $this->buildErrorResponse("The password hash was invalid.");
        }

        return true;
    }

    public function post($restResource, RestHandler $handler = null)
    {
        $username = $restResource["Username"];

        $providerClass = SaasLandlordModule::getCredentialsLoginProviderClassName();

        /**
         * @var LoginProvider $provider
         */
        $provider = new $providerClass();

        try {
            $user = User::findFirst(new Equals($provider->getSettings()->identityColumnName, $username), new Equals("Enabled", true));
            $hash = $user->generatePasswordResetHash();

            $response = new \stdClass();
            $response->PasswordResetHash = $hash;
            $response->Email = $user->Email;
            $response->FullName = $user->FullName;

            $email = Container::instance(ResetPasswordInvitationEmail::class, $user);

            try {
                EmailProvider::selectProviderAndSend($email);
            } catch (EmailException $er){
            }

            return $response;
        }
        catch (RecordNotFoundException $er ){
            return $this->buildErrorResponse("An account identified by ".$username." couldn't be found.");
        }
    }
} 