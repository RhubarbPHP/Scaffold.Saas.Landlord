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

namespace Rhubarb\Scaffolds\Saas\Landlord\Scripts;

use Rhubarb\Scaffolds\NavigationMenu\MenuItem;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;
use Rhubarb\Stem\Schema\Scripts\UpdateSchemas;

include_once("vendor/rhubarbphp/module-stem/src/Modelling/Schema/Scripts/UpdateSchemas.php");

$schemaUpdate = new UpdateSchemas();
$schemaUpdate->generateResponse();

// Create the standard menu items

function forceMenuItem($url, $name, $position = 0)
{
    try {
        $menu = MenuItem::findByUrl($url);
    } catch (RecordNotFoundException $er) {
        $menu = new MenuItem();
        $menu->Url = $url;
    }

    $menu->MenuName = $name;
    $menu->Position = $position;
    $menu->save();
}

forceMenuItem("/", "Home", 100);
forceMenuItem("/accounts/", "Accounts");
forceMenuItem("/users/", "Users");
forceMenuItem("/servers/", "Servers");