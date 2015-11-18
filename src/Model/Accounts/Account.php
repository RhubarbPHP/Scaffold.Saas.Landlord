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

namespace Rhubarb\Scaffolds\Saas\Landlord\Model\Accounts;

use Rhubarb\Scaffolds\Saas\Landlord\Model\Infrastructure\Server;
use Rhubarb\Stem\Aggregates\Count;
use Rhubarb\Stem\Collections\Collection;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Filters\StartsWith;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Schema\Columns\EncryptedString;
use Rhubarb\Stem\Schema\Columns\ForeignKey;
use Rhubarb\Stem\Schema\Columns\String;
use Rhubarb\Stem\Schema\ModelSchema;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\User;

/**
 * A Tenant Account.
 *
 * Tenants usually rely on the landlord to provide them with user authentication, database
 * credentials, and certain security-sensitive operations.
 *
 * @package Rhubarb\Scaffolds\Saas\Landlord\Model\Accounts
 *
 * @property string $AccountID
 * @property int $ServerID
 * @property string $AccountName
 * @property string $CredentialsIV
 *
 * @property User[]|Collection $Users
 */
class Account extends Model
{
    public function createSchema()
    {
        $schema = new ModelSchema("tblAccount");
        $schema->addColumn(
            new String("AccountID", 50),
            new ForeignKey("ServerID"),
            new String("AccountName", 50),
            new EncryptedString("CredentialsIV", 120)
        );

        $schema->uniqueIdentifierColumnName = "AccountID";
        $schema->labelColumnName = "AccountName";

        return $schema;
    }

    protected function beforeSave()
    {
        if ($this->isNewRecord()) {
            // Build a unique reference.
            $baseReference = strtolower(preg_replace("/\W/", "-", $this->AccountName));
            $baseReference = preg_replace("/-+/", "-", $baseReference);
            $baseReference = trim($baseReference, '-');

            // Shorten the base reference. We use the reference to create MySQL databases and usernames. However
            // this can't be any longer than 16 characters. We truncate this now to 10 characters to allow for
            // 5 digits worth of duplication. If we have more than that then we're in a good place!
            if ( strlen( $baseReference ) > 10 )
            {
                $baseReference = substr( $baseReference, 0, 10 );
            }

            $accountsWithBaseReference = (int)count(Account::find(new StartsWith('AccountID', $baseReference . '-'))) + 1;
            $this->AccountID = $baseReference . '-' . $accountsWithBaseReference;

            // Build a CredentialsIV
            $credentialsIV = md5(mt_rand() . uniqid("", true));
            $this->CredentialsIV = $credentialsIV;
        }

        if (!$this->ServerID) {
            $this->ServerID = $this->getNextServer();
        }

        parent::beforeSave();
    }

    protected function getNextServer()
    {
        $servers = Server::find()->addAggregateColumn(new Count("Accounts.AccountID"));
        $servers->addSort("CountOfAccountsAccountID", true);

        return $servers[0]->UniqueIdentifier;
    }

    public function attachUserWithRole(User $user, $tenantRoleID)
    {
        $this->attachUser($user, ['RoleID' => $tenantRoleID]);
    }

    public function attachUser(User $user, $additionalTenantUserProperties = [])
    {
        // Check we're not already attached - in case multiple invitations to the same account are
        // accepted and the tenant doesn't handle it well.
        $users = $this->Users;

        if (!$users->containsUniqueIdentifier($user->UniqueIdentifier)) {
            $this->Users->append($user);
        }

        //$response = TenantGateway::createUser($this, $user, $additionalTenantUserProperties);
    }

    public function detachUser(User $user)
    {
        //TenantGateway::deleteUser($this, $user);
    }
}