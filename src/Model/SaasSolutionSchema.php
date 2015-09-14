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

namespace Rhubarb\Scaffolds\Saas\Landlord\Model;

use Rhubarb\Crown\Context;
use Rhubarb\Crown\Exceptions\ImplementationException;
use Rhubarb\Scaffolds\Saas\Landlord\SaasLandlordModule;
use Rhubarb\Stem\Models\ModelEventManager;
use Rhubarb\Stem\Repositories\MySql\MySql;
use Rhubarb\Stem\Schema\SolutionSchema;
use Rhubarb\Stem\StemSettings;

class SaasSolutionSchema extends SolutionSchema
{
    public function __construct()
    {
        parent::__construct(0.12);

        $this->addModel("User", __NAMESPACE__ . '\Users\User');
        $this->addModel("Account", __NAMESPACE__ . '\Accounts\Account');
        $this->addModel("AccountUser", __NAMESPACE__ . '\Accounts\AccountUser');
        $this->addModel("AccountInvite", __NAMESPACE__ . '\Accounts\AccountInvite');
        $this->addModel("Server", __NAMESPACE__ . '\Infrastructure\Server');

        ModelEventManager::attachEventHandler("Account", "AfterSave", function ($account) {
            $modellingSettings = new StemSettings();

            $context = new Context();

            if (!$context->UnitTesting) {
                $host = $account->Server->Host;
                $port = $account->Server->Port;

                $connection = MySql::getManualConnection(
                    $host,
                    $modellingSettings->Username,
                    $modellingSettings->Password,
                    $port
                );

                $password = sha1($account->UniqueReference . strrev($account->CredentialsIV));

                // Attempt to create the database for this account.
                MySql::executeStatement("CREATE DATABASE IF NOT EXISTS `" . $account->UniqueReference . "`", [], $connection);

                $tenantServers = array_merge(SaasLandlordModule::getTenantServerIPAddresses(), SaasLandlordModule::getTenantServerMasks());
                if (count($tenantServers) === 0) {
                    throw new ImplementationException('No tenant servers defined - use SaasLandlordModule::registerTenantServer() to register some IP Addresses.');
                }
                // grant for all tenant
                foreach ($tenantServers as $tenantServer) {
                    MySql::executeStatement(
                        "GRANT ALL ON `" . $account->UniqueReference . "`.* TO '" . $account->UniqueReference . "'@'" . $tenantServer . "' IDENTIFIED BY '" . $password . "'",
                        [], $connection);
                }
            }
        });
    }

    protected function defineRelationships()
    {
        parent::defineRelationships();

        $this->declareOneToManyRelationships(
            [
                "Server" =>
                    [
                        "Accounts" => "Account.ServerID"
                    ]
            ]);

        $this->declareManyToManyRelationships(
            [
                "Account" =>
                    [
                        "Users" => "AccountUser.AccountID_UserID.User:Accounts",
                        "Invites" => "AccountInvite.AccountID_UserID.User:Invites"
                    ]
            ]);
    }
}