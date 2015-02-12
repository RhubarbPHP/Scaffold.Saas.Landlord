<?php

namespace Gcd\Core\Scaffolds\Saas\UnitTesting;

use Gcd\Core\RestApi\UnitTesting\TokenAuthenticatedRestApiClientTestCase;

class SaasApiTestCase extends TokenAuthenticatedRestApiClientTestCase
{
	use SaasTestCaseTrait;

	function GetApiUri()
	{
		return "/api";
	}

	function GetUsername()
	{
		return "unit-tester";
	}

	protected $_password = "abc123";

	function GetPassword()
	{
		return $this->_password;
	}

	function GetTokensUri()
	{
		return "/tokens";
	}
}