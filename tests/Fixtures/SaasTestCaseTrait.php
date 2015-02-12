<?php

namespace Gcd\Core\Scaffolds\Saas\UnitTesting;

use Gcd\Core\Context;
use Gcd\Core\CoreModule;
use Gcd\Core\Encryption\HashProvider;
use Gcd\Core\Integration\Http\HttpClient;
use Gcd\Core\Modelling\Models\Model;
use Gcd\Core\Modelling\Repositories\Repository;
use Gcd\Core\Modelling\Schema\SolutionSchema;
use Gcd\Core\Module;
use Gcd\Core\Scaffolds\AuthenticationWithRoles\User;
use Gcd\Core\Scaffolds\Saas\Model\Accounts\Account;
use Gcd\Core\Scaffolds\Saas\SaasModule;

trait SaasTestCaseTrait
{
	/**
	 * @var Account
	 */
	protected $_steelInc;

	public static function setUpBeforeClass()
	{
		Repository::SetDefaultRepositoryClassName( "\Gcd\Core\Modelling\Repositories\Offline\Offline" );

		SolutionSchema::ClearSchemas();

		Module::ClearModules();
		Module::RegisterModule( new CoreModule() );
		Module::RegisterModule( new SaasModule() );
		Module::InitialiseModules();

		\Gcd\Core\Layout\LayoutModule::DisableLayout();

		$context = new \Gcd\Core\Context();
		$context->UnitTesting = true;

		$request = Context::CurrentRequest();
		$request->Reset();

		HashProvider::SetHashProviderClassName( "\Gcd\Core\Encryption\Sha512HashProvider" );

		// Make sure HTTP requests go the unit testing route.
		HttpClient::SetDefaultHttpClientClassName( '\Gcd\Core\Integration\Http\UnitTestingHttpClient' );
	}

	protected function setUp()
	{
		Model::DeleteRepositories();

		parent::setUp();

		$user = new User();
		$user->Username = "unit-tester";
		$user->Password = '$6$rounds=10000$saltyfish$xsdN77OODY/XmxLdlkFW9CNxuE4H6NjEGG7K7tGJbzHUyDrVDHROL/FqG.ANet3dcd6WqGOOvaDjLv/WeAtcK0';
		$user->Forename = "Unit Tester";
		$user->Enabled = 1;
		$user->Save();

		$account = new Account();
		$account->AccountName = "Widgets Co";
		$account->Save();

		$account->Users->Append( $user );

		$account = new Account();
		$account->AccountName = "Steel Inc.";
		$account->Save();

		$this->_steelInc = $account;

		$account->Users->Append( $user );

		$unAttachedAccount = new Account();
		$unAttachedAccount->AccountName = "Plastic Molders Ltd.";
		$unAttachedAccount->Save();
	}
} 