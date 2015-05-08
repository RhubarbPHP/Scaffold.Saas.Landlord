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

namespace Rhubarb\Scaffolds\Saas\Landlord\Presenters\Users;

use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\User;
use Rhubarb\Leaf\Presenters\Application\Table\Table;
use Rhubarb\Leaf\Views\HtmlView;

class UsersCollectionView extends HtmlView
{
    protected $table;

    public function createPresenters()
    {
        parent::createPresenters();

        $this->addPresenters(
            $this->table = new Table(User::findTenantUsers())
        );

        $this->table->Columns =
            [
                "Forename",
                "Surname",
                "Email",
                "Username",
                "" => "<a href=\"{UserID}/\">View</a>"
            ];
    }

    protected function printViewContent()
    {
        print $this->table;
    }
}