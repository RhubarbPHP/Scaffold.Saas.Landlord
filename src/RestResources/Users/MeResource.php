<?php

namespace Rhubarb\Crown\Scaffolds\Saas\RestResources\Users;

use Rhubarb\Crown\Scaffolds\Saas\LoginProviders\SaasLoginProvider;

class MeResource extends UserResource
{
	public function GetModel()
	{
		$login = new SaasLoginProvider();
		$user = $login->GetModel();

		return $user;
	}
} 