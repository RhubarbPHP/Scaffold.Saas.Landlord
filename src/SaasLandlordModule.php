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
use Rhubarb\Leaf\Crud\UrlHandlers\CrudUrlHandler;
use Rhubarb\RestApi\Resources\ApiDescriptionResource;
use Rhubarb\RestApi\Resources\ModelRestResource;
use Rhubarb\RestApi\UrlHandlers\RestApiRootHandler;
use Rhubarb\RestApi\UrlHandlers\RestCollectionHandler;
use Rhubarb\RestApi\UrlHandlers\RestResourceHandler;
use Rhubarb\RestApi\UrlHandlers\UnauthenticatedRestCollectionHandler;
use Rhubarb\RestApi\UrlHandlers\UnauthenticatedRestResourceHandler;
use Rhubarb\Scaffolds\Authentication\Settings\AuthenticationSettings;
use Rhubarb\Scaffolds\AuthenticationWithRoles\AuthenticationWithRolesModule;
use Rhubarb\Scaffolds\NavigationMenu\NavigationMenuModule;
use Rhubarb\Scaffolds\Saas\Landlord\Layouts\LandlordLayout;
use Rhubarb\Scaffolds\Saas\Landlord\LoginProviders\LandlordLoginProvider;
use Rhubarb\Scaffolds\Saas\Landlord\RestAuthenticationProviders\CredentialsAuthenticationProvider;
use Rhubarb\Scaffolds\Saas\Landlord\RestAuthenticationProviders\TokenBasedAuthenticationProvider;
use Rhubarb\Scaffolds\Saas\Landlord\RestResources\Accounts\AccountInviteResource;
use Rhubarb\Scaffolds\Saas\Landlord\RestResources\Accounts\AccountResource;
use Rhubarb\Scaffolds\Saas\Landlord\RestResources\Accounts\ServerResource;
use Rhubarb\Scaffolds\Saas\Landlord\RestResources\Users\UserInviteResource;
use Rhubarb\Scaffolds\Saas\Landlord\RestResources\Users\UserResource;
use Rhubarb\Scaffolds\TokenBasedRestApi\TokenBasedRestApiModule;
use Rhubarb\Stem\Schema\SolutionSchema;

class SaasLandlordModule extends Module
{
    private $apiStubUrl;

    public function __construct($apiStubUrl = "/api", $identityColumnName = "Username")
    {
        $this->apiStubUrl = $apiStubUrl;

        $settings = AuthenticationSettings::singleton();
        $settings->identityColumnName = $identityColumnName;

        parent::__construct();
    }

    protected function initialise()
    {
        SolutionSchema::registerSchema("SaasSchema", __NAMESPACE__ . "\Model\SaasSolutionSchema");

        EncryptionProvider::setProviderClassName('\Rhubarb\Scaffolds\Saas\Landlord\EncryptionProviders\SaasAes256EncryptionProvider');

        parent::initialise();
    }

    protected function getModules()
    {
        return [
            new LayoutModule(LandlordLayout::class),
            new NavigationMenuModule(),
            new AuthenticationWithRolesModule(LandlordLoginProvider::class),
            new TokenBasedRestApiModule(
            CredentialsAuthenticationProvider::class,
            TokenBasedAuthenticationProvider::class
        )];
    }

    protected function registerUrlHandlers()
    {
        parent::registerUrlHandlers();

        ModelRestResource::registerModelToResourceMapping("Server", ServerResource::class );
        ModelRestResource::registerModelToResourceMapping("Account", AccountResource::class );

        $rootApiUrl = new RestApiRootHandler( ApiDescriptionResource::class,
            [
                "/users/me" => new RestResourceHandler(__NAMESPACE__ . '\RestResources\Users\MeResource',
                    [
                        "/accounts" => new RestCollectionHandler(__NAMESPACE__ . '\RestResources\Accounts\AccountResource'),
                        // For users to manage their own invites
                        "/invites" => new RestCollectionHandler(UserInviteResource::class, [], ["get", "put"])
                    ]),
                "/users" => new UnauthenticatedRestCollectionHandler(__NAMESPACE__ . '\RestResources\Users\UserResource',
                    [
                        "/password-reset-invitations" => new UnauthenticatedRestResourceHandler(__NAMESPACE__ . '\RestResources\Users\PasswordResetInvitationResource', [], ["post", "put"]),
                    ], ["post"]),
                "/accounts" => new RestCollectionHandler(__NAMESPACE__ . '\RestResources\Accounts\AccountResource',
                    [
                       "/users" => new RestCollectionHandler( UserResource::class, [], ["get", "post", "put" ] ),
                        // For invite management on the tenant system
                        "/invites" => new RestCollectionHandler( AccountInviteResource::class, [], ["get", "post", "put" ] )
                    ]),
            ] );

        $rootApiUrl->setPriority(20);

        $this->addUrlHandlers(
            [
                $this->apiStubUrl => $rootApiUrl
            ]
        );

        $this->addUrlHandlers(
            [
                "/accounts/" => new CrudUrlHandler("Account", 'Rhubarb\Scaffolds\Saas\Landlord\Leaves\Accounts'),
                "/users/" => new CrudUrlHandler("User", 'Rhubarb\Scaffolds\Saas\Landlord\Leaves\Users'),
                "/servers/" => new CrudUrlHandler("Server", 'Rhubarb\Scaffolds\Saas\Landlord\Leaves\Servers'),
                "/" => new ClassMappedUrlHandler('\Rhubarb\Scaffolds\Saas\Landlord\Leaves\Index'),
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