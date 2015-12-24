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

use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\User;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Schema\Columns\AutoIncrementColumn;
use Rhubarb\Stem\Schema\Columns\ForeignKeyColumn;
use Rhubarb\Stem\Schema\Columns\StringColumn;
use Rhubarb\Stem\Schema\ModelSchema;

/**
 * Class AccountUser
 * @package Rhubarb\Scaffolds\Saas\Landlord\Model\Accounts
 *
 * @property Account $Account
 * @property User $User
 */
class AccountUser extends Model
{
    public function createSchema()
    {
        $schema = new ModelSchema("tblAccountUser");

        $schema->addColumn(
            new AutoIncrementColumn("AccountUserID"),
            new StringColumn("AccountID", 16),
            new ForeignKeyColumn("UserID")
        );

        return $schema;
    }

    protected function beforeDelete()
    {
        $this->Account->detachUser( $this->User );
    }

    public static function updateUserOnAllAccounts(User $user)
    {
        $accountUsers = self::find( new Equals( 'UserID', $user->UserID ) );

        foreach( $accountUsers as $accountUser )
        {
            //TenantGateway::updateUser($accountUser->Account, $user);
        }
    }
}