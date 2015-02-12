<?php

namespace Rhubarb\Crown\Scaffolds\Saas\RestAuthenticationProviders;

use Rhubarb\Crown\RestApi\Authentication\ModelLoginProviderAuthenticationProvider;

class CredentialsAuthenticationProvider extends ModelLoginProviderAuthenticationProvider
{
	protected function GetLoginProviderClassName()
	{
		return "\Rhubarb\Crown\Scaffolds\Saas\LoginProviders\SaasLoginProvider";
	}
}