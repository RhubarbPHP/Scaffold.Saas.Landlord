<?php

namespace Rhubarb\Scaffolds\Saas\RestResources\Users;

use Rhubarb\Crown\Logging\Log;
use Rhubarb\Stem\Collections\Collection;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Schema\SolutionSchema;
use Rhubarb\Crown\RestApi\Exceptions\UpdateException;
use Rhubarb\Crown\RestApi\Resources\ModelRestResource;
use Rhubarb\Crown\RestApi\UrlHandlers\RestHandler;
use string;

class UserResource extends ModelRestResource
{
	/**
	 * Returns the name of the model to use for this resource.
	 *
	 * @return string
	 */
	public function GetModelName()
	{
		return "User";
	}

	protected function GetColumns()
	{
		return [ "Username", "Forename", "Surname", "Email", "Enabled" ];
	}

	protected function BeforeModelUpdated($model, $restResource)
	{
		if ( isset( $restResource[ "NewPassword" ] ) )
		{
			Log::Debug( "User `".$model->RealName."` (`".$model->UniqueIdentifier."`) password changed.", "SaaS" );

			$model->SetNewPassword( $restResource[ "NewPassword" ] );
		}
	}
}