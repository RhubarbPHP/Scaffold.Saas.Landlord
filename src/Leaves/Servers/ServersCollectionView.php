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

namespace Rhubarb\Scaffolds\Saas\Landlord\Leaves\Servers;

use Rhubarb\Scaffolds\Saas\Landlord\Model\Accounts\Account;
use Rhubarb\Leaf\Table\Leaves\Table;
use Rhubarb\Leaf\Views\View;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Infrastructure\Server;

class ServersCollectionView extends View
{
    protected $table;

    protected function createSubLeaves()
    {
        parent::createSubLeaves();

        $this->registerSubLeaf(
            $this->table = new Table(Server::find())
        );

        $this->table->columns =
            [
                "ServerName",
                "Host",
                "Port",
                "" => "<a href=\"{ServerID}/\">View</a>"
            ];
    }

    protected function printViewContent()
    {
        print $this->table;
    }
}