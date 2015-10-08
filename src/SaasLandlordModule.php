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
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\UrlHandlers\ClassMappedUrlHandler;
use Rhubarb\Patterns\Mvp\Crud\CrudUrlHandler;
use Rhubarb\RestApi\Resources\ApiDescriptionResource;
use Rhubarb\RestApi\Resources\ModelRestResource;
use Rhubarb\RestApi\UrlHandlers\RestApiRootHandler;
use Rhubarb\RestApi\UrlHandlers\RestCollectionHandler;
use Rhubarb\RestApi\UrlHandlers\RestResourceHandler;
use Rhubarb\RestApi\UrlHandlers\UnauthenticatedRestCollectionHandler;
use Rhubarb\RestApi\UrlHandlers\UnauthenticatedRestResourceHandler;
use Rhubarb\Scaffolds\AuthenticationWithRoles\AuthenticationWithRolesModule;
use Rhubarb\Scaffolds\NavigationMenu\NavigationMenuModule;
use Rhubarb\Scaffolds\Saas\Landlord\RestResources\Accounts\ServerResource;
use Rhubarb\Scaffolds\TokenBasedRestApi\TokenBasedRestApiModule;
use Rhubarb\Stem\Schema\SolutionSchema;

class SaasLandlordModule extends Module
{
    private $apiStubUrl;

    public function __construct($apiStubUrl = "/api")
    {
        $this->apiStubUrl = $apiStubUrl;
    }

    protected function initialise()
    {
        SolutionSchema::registerSchema("SaasSchema", __NAMESPACE__ . "\Model\SaasSolutionSchema");

        EncryptionProvider::setEncryptionProviderClassName('\Rhubarb\Scaffolds\Saas\Landlord\EncryptionProviders\SaasAes256EncryptionProvider');

        parent::initialise();
    }

    protected function registerDependantModules()
    {
        parent::registerDependantModules();

        Module::registerModule(new LayoutModule('\Rhubarb\Scaffolds\Saas\Landlord\Layouts\LandlordLayout'));
        Module::registerModule(new NavigationMenuModule());

        Module::registerModule(new AuthenticationWithRolesModule('\Rhubarb\Scaffolds\Saas\Landlord\LoginProviders\LandlordLoginProvider'));

        Module::registerModule(new TokenBasedRestApiModule(
            '\Rhubarb\Scaffolds\Saas\Landlord\RestAuthenticationProviders\CredentialsAuthenticationProvider',
            '\Rhubarb\Scaffolds\Saas\Landlord\RestAuthenticationProviders\TokenBasedAuthenticationProvider'
        ));
    }

    protected function registerUrlHandlers()
    {
        parent::registerUrlHandlers();

        ModelRestResource::registerModelToResourceMapping("Server", ServerResource::class );

        $rootApiUrl = new RestApiRootHandler( ApiDescriptionResource::class,
            [
                "/users/me" => new RestResourceHandler(__NAMESPACE__ . '\RestResources\Users\MeResource',
                    [
                        "/accounts" => new RestCollectionHandler(__NAMESPACE__ . '\RestResources\Accounts\AccountResource')
                    ]),
                "/users" => new UnauthenticatedRestCollectionHandler(__NAMESPACE__ . '\RestResources\Users\UserResource',
                    [
                        "/password-reset-invitations" => new UnauthenticatedRestResourceHandler(__NAMESPACE__ . '\RestResources\Users\PasswordResetInvitationResource', [], ["post", "put"]),
                    ], ["post"]),
                "/accounts" => new RestCollectionHandler(__NAMESPACE__ . '\RestResources\Accounts\AccountResource')
            ] );

        $rootApiUrl->setPriority(20);

        $this->addUrlHandlers(
            [
                $this->apiStubUrl => $rootApiUrl
            ]
        );

        $this->addUrlHandlers(
            [
                "/accounts/" => new CrudUrlHandler("Account", 'Rhubarb\Scaffolds\Saas\Landlord\Presenters\Accounts'),
                "/users/" => new CrudUrlHandler("User", 'Rhubarb\Scaffolds\Saas\Landlord\Presenters\Users'),
                "/" => new ClassMappedUrlHandler('\Rhubarb\Scaffolds\Saas\Landlord\Presenters\IndexPresenter'),
            ]);
    }

    protected static $tenantServerIPAddresses = [];
    protected static $tenantServerMasks = [];

    /**
     * @param string $tenantServer An ip address or CIDR subnet mask
     */
    public static function registerTenantServer($tenantServer)
    {
        if (strpos($tenantServer, '/') !== false) {
            self::registerTenantServerMask($tenantServer);
        } else {
            self::registerTenantServerIPAddress($tenantServer);
        }
    }

    /**
     * @param string $mask
     */
    public static function registerTenantServerMask($mask)
    {
        self::$tenantServerMasks[] = $mask;
    }

    /**
     * @param string $ipAddress
     */
    public static function registerTenantServerIPAddress($ipAddress)
    {
        self::$tenantServerIPAddresses[] = $ipAddress;
    }

    public static function clearRegisteredTenantServers()
    {
        self::$tenantServerIPAddresses = [];
        self::$tenantServerMasks = [];
    }

    /**
     * @return array
     */
    public static function getTenantServerIPAddresses()
    {
        return self::$tenantServerIPAddresses;
    }

    /**
     * @return array
     */
    public static function getTenantServerMasks()
    {
        return self::$tenantServerMasks;
    }

    /**
     * @param string $ipAddress
     *
     * @return bool
     */
    public static function isTenantServer($ipAddress)
    {
        if (in_array($ipAddress, self::getTenantServerIPAddresses())) {
            return true;
        }
        foreach (self::getTenantServerMasks() as $mask) {
            if (self::checkIPAddressAgainstCIDRMask($ipAddress, $mask)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $ipAddress
     * @param string $CIDRMask
     *
     * @return bool
     */
    protected static function checkIPAddressAgainstCIDRMask($ipAddress, $CIDRMask)
    {
        list( $mask, $CIDRSuffix ) = explode('/', $CIDRMask);

        return ( ip2long($ipAddress) & ~( ( 1 << ( 32 - $CIDRSuffix ) ) - 1 ) ) == ip2long($mask);
    }

}