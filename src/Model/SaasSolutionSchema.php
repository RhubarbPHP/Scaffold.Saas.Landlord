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

use Rhubarb\Crown\Application;
use Rhubarb\Crown\Exceptions\ImplementationException;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\Invite;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\User;
use Rhubarb\Scaffolds\Saas\Landlord\SaasLandlordModule;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Models\ModelEventManager;
use Rhubarb\Stem\Repositories\MySql\MySql;
use Rhubarb\Stem\Schema\SolutionSchema;
use Rhubarb\Stem\StemSettings;

class SaasSolutionSchema extends SolutionSchema
{
    public function __construct()
    {
        parent::__construct(0.13);

        $this->addModel("User", __NAMESPACE__ . '\Users\User');
        $this->addModel("Invite", __NAMESPACE__ . '\Users\Invite');
        $this->addModel("Account", __NAMESPACE__ . '\Accounts\Account');
        $this->addModel("AccountUser", __NAMESPACE__ . '\Accounts\AccountUser');
        $this->addModel("Server", __NAMESPACE__ . '\Infrastructure\Server');

        ModelEventManager::attachEventHandler("Account", "AfterSave", function ($account) {
            $modellingSettings = StemSettings::singleton();

            if (!Application::current()->unitTesting) {
                $host = $account->Server->Host;
                $port = $account->Server->Port;

                $connection = MySql::getManualConnection(
                    $host,
                    $modellingSettings->Username,
                    $modellingSettings->Password,
                    $port
                );

                $password = sha1($account->AccountID . strrev($account->CredentialsIV));

                // Attempt to create the database for this account.
                MySql::executeStatement("CREATE DATABASE IF NOT EXISTS `" . $account->AccountID . "`", [], $connection);

                $tenantServers = array_merge(SaasLandlordModule::getTenantServerIPAddresses(), SaasLandlordModule::getTenantServerMasks());
                if (count($tenantServers) === 0) {
                    throw new ImplementationException('No tenant servers defined - use SaasLandlordModule::registerTenantServer() to register some IP Addresses.');
                }
                // grant for all tenant
                foreach ($tenantServers as $tenantServer) {
                    MySql::executeStatement(
                        "GRANT ALL ON `" . $account->AccountID . "`.* TO '" . $account->AccountID . "'@'" . $tenantServer . "' IDENTIFIED BY '" . $password . "'",
                        [], $connection);
                }
            }
        });

        ModelEventManager::attachEventHandler("Invite", "BeforeSave", function (Invite $invite) {
            if( $invite->isNewRecord() )
            {
                try {
                    $user = User::findFirst(new Equals('Email', $invite->Email));
                } catch (RecordNotFoundException $ex) {
                    $user = new User();
                    $user->Enabled = false;
                    $user->Email = $invite->Email;
                    $user->save();
                }
                $invite->UserID = $user->UniqueIdentifier;
            }

            // Accept invites
            if( $invite->Accepted && $invite->hasPropertyChanged( 'Accepted' ) )
            {
                $invite->Account->attachUser($invite->User);
            }

        });

        ModelEventManager::attachEventHandler("Invite", "AfterSave", function (Invite $invite) {
            $invite->send();
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
                    ],
                "User" =>
                    [
                        "Invites" => "Invite.UserID"
                    ],
                "Account" =>
                    [
                        "Invites" => "Invite.AccountID"
                    ],

            ]);

        $this->declareManyToManyRelationships(
            [
                "Account" =>
                    [
                        "Users" => "AccountUser.AccountID_UserID.User:Accounts",
                    ]
            ]);
    }
}