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

namespace Rhubarb\Scaffolds\Saas\Landlord;

use Rhubarb\Crown\Encryption\EncryptionProvider;
use Rhubarb\Crown\Module;
use Rhubarb\RestApi\Resources\RestResource;
use Rhubarb\RestApi\UrlHandlers\PostOnlyRestCollectionHandler;
use Rhubarb\RestApi\UrlHandlers\RestCollectionHandler;
use Rhubarb\RestApi\UrlHandlers\RestResourceHandler;
use Rhubarb\RestApi\UrlHandlers\UnauthenticatedRestCollectionHandler;
use Rhubarb\Scaffolds\AuthenticationWithRoles\AuthenticationWithRolesModule;
use Rhubarb\Scaffolds\TokenBasedRestApi\TokenBasedRestApiModule;
use Rhubarb\Stem\Schema\SolutionSchema;

class SaasModule extends Module
{
    private $apiStubUrl;

    public function __construct($apiStubUrl = "/api/")
    {
        parent::__construct();

        $this->apiStubUrl = $apiStubUrl;
    }

    protected function initialise()
    {
        SolutionSchema::registerSchema("SaasSchema", __NAMESPACE__ . "\Model\SaasSolutionSchema");

        parent::initialise();
    }

    protected function registerDependantModules()
    {
        parent::registerDependantModules();

        Module::registerModule(new AuthenticationWithRolesModule('\Rhubarb\Scaffolds\Saas\Landlord\LoginProviders\SaasLoginProvider'));

        Module::registerModule(new TokenBasedRestApiModule(
            '\Rhubarb\Scaffolds\Saas\Landlord\RestAuthenticationProviders\CredentialsAuthenticationProvider',
            '\Rhubarb\Scaffolds\Saas\Landlord\RestAuthenticationProviders\TokenBasedAuthenticationProvider'
        ));
    }

    protected function registerUrlHandlers()
    {
        parent::registerUrlHandlers();

        RestResource::registerCanonicalResourceUrl(__NAMESPACE__ . '\RestResources\Accounts\AccountResource',
            "/api/accounts");

        $urlHandlers =
            [
                $this->apiStubUrl . "users" => new UnauthenticatedRestCollectionHandler(__NAMESPACE__ . '\RestResources\Users\UserResource',
                    [
                        "/me" => new RestResourceHandler(__NAMESPACE__ . '\RestResources\Users\MeResource',
                            [
                                "/accounts" => new RestCollectionHandler(__NAMESPACE__ . '\RestResources\Accounts\AccountResource')
                            ])
                    ], ["post"]),
                $this->apiStubUrl . "accounts" => new PostOnlyRestCollectionHandler(__NAMESPACE__ . '\RestResources\Accounts\AccountResource')
            ];

        foreach ($urlHandlers as $handler) {
            $handler->setPriority(20);
        }

        $this->addUrlHandlers($urlHandlers);

        EncryptionProvider::setEncryptionProviderClassName('\Rhubarb\Scaffolds\Saas\Landlord\EncryptionProviders\SaasAes256EncryptionProvider');
    }
}