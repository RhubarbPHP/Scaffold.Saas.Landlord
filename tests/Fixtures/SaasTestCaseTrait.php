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

namespace Rhubarb\Scaffolds\Saas\Landlord\Tests\Fixtures;

use Rhubarb\Crown\Context;
use Rhubarb\Crown\Encryption\HashProvider;
use Rhubarb\Crown\Http\HttpClient;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Repositories\Repository;
use Rhubarb\Stem\Schema\SolutionSchema;
use Rhubarb\Crown\Module;
use Rhubarb\Scaffolds\AuthenticationWithRoles\User;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Accounts\Account;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Infrastructure\Server;
use Rhubarb\Scaffolds\Saas\Landlord\SaasLandlordModule;

trait SaasTestCaseTrait
{
	/**
	 * @var Account
	 */
	protected $steelInc;

	/**
	 * @var User
	 */
	protected $nigel;

	protected $landlordUser;

	/**
	 * @var Account
	 */
	protected $protonWelding;

	public static function setUpBeforeClass()
	{
		Repository::setDefaultRepositoryClassName( "\Rhubarb\Stem\Repositories\Offline\Offline" );

		SolutionSchema::clearSchemas();

		Module::clearModules();
		Module::registerModule( new SaasLandlordModule() );
		Module::initialiseModules();

		LayoutModule::disableLayout();

		$context = new Context();
		$context->UnitTesting = true;

		$request = Context::currentRequest();
		$request->reset();

		HashProvider::setHashProviderClassName( "\Rhubarb\Crown\Encryption\Sha512HashProvider" );

		// Make sure HTTP requests go the unit testing route.
		HttpClient::setDefaultHttpClientClassName( '\Rhubarb\Crown\Tests\Fixtures\UnitTestingHttpClient' );
	}

	protected function setUp()
	{
		Model::deleteRepositories();

		parent::setUp();

		$server = new Server();
		$server->ServerName = "proton";
		$server->Host = "1.2.3.5";
		$server->Port = "9876";
		$server->save();

		$server = new Server();
		$server->ServerName = "electron";
		$server->Host = "1.2.3.4";
		$server->Port = "9876";
		$server->save();

		$user = new User();
		$user->Username = "unit-tester";
		$user->Password = '$6$rounds=10000$saltyfish$xsdN77OODY/XmxLdlkFW9CNxuE4H6NjEGG7K7tGJbzHUyDrVDHROL/FqG.ANet3dcd6WqGOOvaDjLv/WeAtcK0';
		$user->Forename = "Unit Tester";
		$user->Email = "ut@ut.com";
		$user->Enabled = 1;
		$user->save();

		$account = new Account();
		$account->AccountName = "Widgets Co";
		$account->ServerID = $server->UniqueIdentifier;
		$account->save();

		$account->Users->append( $user );

		$account = new Account();
		$account->AccountName = "Steel Inc.";
		$account->save();

		$this->steelInc = $account;

		$account->Users->append( $user );

		$user = new User();
		$user->Username = "nigel";
		$user->Password = '$6$rounds=10000$saltyfish$xsdN77OODY/XmxLdlkFW9CNxuE4H6NjEGG7K7tGJbzHUyDrVDHROL/FqG.ANet3dcd6WqGOOvaDjLv/WeAtcK0';
		$user->Forename = "Nigel";
		$user->Surname = "Stevenson";
		$user->Enabled = 1;
		$user->Email = "bignige@ut.com";
		$user->save();

		$this->nigel = $user;

		$account = new Account();
		$account->AccountName = "Proton Welding";
		$account->save();

		$this->protonWelding = $account;

		$account->Users->append( $user );

		$user = new User();
		$user->Username = "norma";
		$user->Password = '$6$rounds=10000$saltyfish$xsdN77OODY/XmxLdlkFW9CNxuE4H6NjEGG7K7tGJbzHUyDrVDHROL/FqG.ANet3dcd6WqGOOvaDjLv/WeAtcK0';
		$user->Forename = "Norma";
		$user->Enabled = 0;
		$user->Email = "norma@ut.com";
		$user->save();

		$unAttachedAccount = new Account();
		$unAttachedAccount->AccountName = "Plastic Molders Ltd.";
		$unAttachedAccount->save();

		$this->landlordUser = new User();
		$this->landlordUser->Username = "admin";
		$this->landlordUser->Forename = "Administrator";
		$this->landlordUser->setNewPassword( "admin" );
		$this->landlordUser->Enabled = true;
		$this->landlordUser->LandlordUser = true;
		$this->landlordUser->save();
	}
} 