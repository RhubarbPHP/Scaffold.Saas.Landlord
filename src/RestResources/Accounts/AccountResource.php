<?php

namespace Rhubarb\Scaffolds\Saas\RestResources\Accounts;

use Rhubarb\Crown\Logging\Log;
use Rhubarb\Crown\RestApi\Resources\ModelRestResource;
use Rhubarb\Scaffolds\Saas\LoginProviders\SaasLoginProvider;

class AccountResource extends ModelRestResource
{

	/**
	 * Returns the name of the model to use for this resource.
	 *
	 * @return string
	 */
	public function GetModelName()
	{
		return "Account";
	}

	public function AfterModelCreated($model, $restResource)
	{
		Log::Debug( "Account `".$model->AccountName."` created", "SaaS" );

		// Make sure that new accounts are attached to the authenticated user.
		$login = new SaasLoginProvider();
		$user = $login->GetModel();

		$user->Accounts->Append( $model );
	}
}