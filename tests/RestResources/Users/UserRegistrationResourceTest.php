<?php

namespace Gcd\Core\Scaffolds\Saas\RestResources\Users;

use Gcd\Core\Modelling\Filters\Equals;
use Gcd\Core\Scaffolds\AuthenticationWithRoles\User;
use Gcd\Core\Scaffolds\Saas\UnitTesting\SaasApiTestCase;

class UserRegistrationResourceTest extends SaasApiTestCase
{
	public function GetUsername()
	{
		// Forcibly disable authentication
		return "";
	}

	public function GetPassword()
	{
		// Forcibly disable authentication
		return "";
	}

	protected function GetToken()
	{
		return "faketoken";
	}

	public function testPublicCanRegister()
	{
		// Test you must supply key details
		$result = $this->MakeApiCall( "/users", "post", [
			"Username" => ""
		]);

		$this->assertFalse( $result->result->status );

		$result = $this->MakeApiCall( "/users", "post", [
			"Username" => "abc123",
			"Forename" => ""
		]);

		$this->assertFalse( $result->result->status );

		$result = $this->MakeApiCall( "/users", "post", [
			"Username" => "abc123",
			"Forename" => "test",
			"NewPassword" => "abc",
		]);

		$this->assertEquals( "test", $result->Forename );

		$result = $this->MakeApiCall( "/users", "post", [
			"Username" => "nancy",
			"NewPassword" => "bell",
			"Email" => "jbloggs@hotmail.com",
			"Forename" => "Nancy",
			"Surname" => "Bell"
		]);

		$this->assertEquals( $result->Email, User::FindFirst( new Equals( "Username", "nancy" ) )->Email );

		// Test you can't update users through this call.

		$result = $this->MakeApiCall( "/users/".$result->_id, "put", $result );

		$this->assertFalse( $result->result->status );

		// Test you can't create a duplicate on username.
		$result = $this->MakeApiCall( "/users", "post", [
			"Username" => "nancy",
			"NewPassword" => "bell",
			"Email" => "jbloggs@hotmail.com",
			"Forename" => "Nancy",
			"Surname" => "Bell"
		]);

		$this->assertFalse( $result->result->status );
	}
}