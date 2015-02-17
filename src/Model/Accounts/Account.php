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
use Rhubarb\Scaffolds\Authentication\User;
use Rhubarb\Stem\Aggregates\Count;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\AutoIncrement;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\EncryptedVarchar;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\ForeignKey;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\Varchar;
use Rhubarb\Stem\Repositories\MySql\Schema\MySqlSchema;

class Account extends Model
{
    public function createSchema()
    {
        $schema = new MySqlSchema("tblAccount");
        $schema->addColumn(
            new AutoIncrement("AccountID"),
            new ForeignKey("ServerID"),
            new Varchar("AccountName", 50),
            new Varchar("UniqueReference", 50),
            new EncryptedVarchar("CredentialsIV", 120)
        );

        $schema->labelColumnName = "AccountName";

        return $schema;
    }

    public function invite(User $user)
    {
        $this->Invites->append($user);
    }

    protected function beforeSave()
    {
        if ($this->isNewRecord()) {
            // Build a unique reference.
            $baseReference = strtolower(preg_replace("/\W/", "-", $this->AccountName));
            $baseReference = preg_replace("/-+/", "-", $baseReference);
            $baseReference = trim($baseReference, '-');

            $reference = $baseReference;

            $dupeCount = 1;

            do {
                $duped = false;
                $list = Account::find(new Equals("UniqueReference", $reference));

                if (count($list) > 0) {
                    $dupeCount++;

                    $reference = $baseReference . "-" . $dupeCount;

                    $duped = true;
                }
            } while ($duped);

            $this->UniqueReference = $reference;

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
}