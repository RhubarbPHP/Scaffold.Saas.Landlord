<?php

namespace Rhubarb\Crown\Scaffolds\Saas\RestAuthenticationProviders;

use Rhubarb\Crown\Modelling\Models\Model;
use Rhubarb\Crown\Scaffolds\Saas\LoginProviders\SaasLoginProvider;
use Rhubarb\Crown\Scaffolds\TokenBasedRestApi;

class TokenBasedAuthenticationProvider extends TokenBasedRestApi\Authentication\TokenAuthenticationProvider
{
	protected function LogUserIn(Model $user)
	{
		$loginProvider = new SaasLoginProvider();
		$loginProvider->ForceLogin( $user );
	}
}