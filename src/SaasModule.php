<?php

namespace Rhubarb\Scaffolds\Saas;

use Rhubarb\Crown\Encryption\EncryptionProvider;
use Rhubarb\Stem\Schema\SolutionSchema;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\RestApi\Resources\RestResource;
use Rhubarb\Crown\RestApi\RestApiModule;
use Rhubarb\Crown\RestApi\UrlHandlers\PostOnlyRestCollectionHandler;
use Rhubarb\Crown\RestApi\UrlHandlers\RestCollectionHandler;
use Rhubarb\Crown\RestApi\UrlHandlers\RestResourceHandler;
use Rhubarb\Crown\RestApi\UrlHandlers\UnauthenticatedRestCollectionHandler;
use Rhubarb\Scaffolds\AuthenticationWithRoles\AuthenticationWithRolesModule;
use Rhubarb\Scaffolds\TokenBasedRestApi\TokenBasedRestApiModule;

class SaasModule extends Module
{
	private $_apiStubUrl;

    public function __construct( $apiStubUrl = "/api/" )
    {
        $this->namespace = __NAMESPACE__;
        $this->AddClassPath( __DIR__ );

		$this->_apiStubUrl = $apiStubUrl;
    }

	protected function Initialise()
	{
		SolutionSchema::RegisterSchema( "SaasSchema", __NAMESPACE__."\Model\SaasSolutionSchema" );

		parent::Initialise();
	}

	protected function RegisterDependantModules()
    {
		parent::RegisterDependantModules();

		Module::RegisterModule( new AuthenticationWithRolesModule( '\Rhubarb\Scaffolds\Saas\LoginProviders\SaasLoginProvider' ) );

		Module::RegisterModule( new TokenBasedRestApiModule(
			'\Rhubarb\Scaffolds\Saas\RestAuthenticationProviders\CredentialsAuthenticationProvider',
			'\Rhubarb\Scaffolds\Saas\RestAuthenticationProviders\TokenBasedAuthenticationProvider'
			));
		Module::RegisterModule( new RestApiModule() );
    }

	protected function RegisterUrlHandlers()
	{
		parent::RegisterUrlHandlers();

		RestResource::RegisterCanonicalResourceUrl( __NAMESPACE__.'\RestResources\Accounts\AccountResource', "/api/accounts" );

		$urlHandlers =
		[
			$this->_apiStubUrl."users" => new UnauthenticatedRestCollectionHandler( __NAMESPACE__.'\RestResources\Users\UserResource',
			[
				"/me" => new RestResourceHandler( __NAMESPACE__.'\RestResources\Users\MeResource',
				[
					"/accounts" => new RestCollectionHandler( __NAMESPACE__.'\RestResources\Accounts\AccountResource' )
				])
			], [ "post" ] ),
			$this->_apiStubUrl."accounts" => new PostOnlyRestCollectionHandler( __NAMESPACE__.'\RestResources\Accounts\AccountResource' )
		];

		foreach( $urlHandlers as $handler )
		{
			$handler->SetPriority( 20 );
		}

		$this->AddUrlHandlers( $urlHandlers );

		EncryptionProvider::SetEncryptionProviderClassName( '\Rhubarb\Scaffolds\Saas\EncryptionProviders\SaasAes256EncryptionProvider' );
	}
}