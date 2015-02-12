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

namespace Rhubarb\Scaffolds\Saas\Model\Infrastructure;

use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\AutoIncrement;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\EncryptedVarchar;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\Varchar;
use Rhubarb\Stem\Repositories\MySql\Schema\MySqlSchema;

class Server extends Model
{
    /**
     * Returns the schema for this data object.
     *
     * @return \Rhubarb\Stem\Schema\ModelSchema
     */
    public function createSchema()
    {
        $schema = new MySqlSchema("tblServer");
        $schema->addColumn(
            new AutoIncrement("ServerID"),
            new Varchar("ServerName", 50),
            new EncryptedVarchar("Host", 40),
            new EncryptedVarchar("Port", 10)
        );

        return $schema;
    }
}