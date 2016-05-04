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

namespace Rhubarb\Scaffolds\Saas\Landlord\Leaves\Users;

use Rhubarb\Leaf\Crud\Leaves\CrudView;

class UsersItemView extends CrudView
{
    protected function createSubLeaves()
    {
        parent::createSubLeaves();

        $this->registerSubLeaf(
            "Forename",
            "Surname",
            "Email",
            "Enabled"
        );
    }

    protected function printViewContent()
    {
        $this->layoutItemsWithContainer("Profile Details",
            [
                "Name" => "{Forename} {Surname}",
                "Email",
                "" => "{Enabled} Login Enabled"
            ]);

        print $this->leaves["Save"];
        print $this->leaves["Cancel"];
    }
}