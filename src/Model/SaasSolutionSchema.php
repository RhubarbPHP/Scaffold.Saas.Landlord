<?php

namespace Rhubarb\Crown\Scaffolds\Saas\Model;

use Rhubarb\Crown\Modelling\Schema\SolutionSchema;

class SaasSolutionSchema extends SolutionSchema
{
	public function __construct()
	{
		parent::__construct( 0.1 );

		$this->AddModel( "Account", __NAMESPACE__.'\Accounts\Account' );
		$this->AddModel( "AccountUser", __NAMESPACE__.'\Accounts\AccountUser' );
		$this->AddModel( "AccountInvite", __NAMESPACE__.'\Accounts\AccountInvite' );
		$this->AddModel( "Server", __NAMESPACE__.'\Infrastructure\Server' );
	}

	protected function DefineRelationships()
	{
		parent::DefineRelationships();

		$this->DeclareManyToManyRelationships(
		[
			"Account" =>
			[
				"Users" => "AccountUser.AccountID_UserID.User:Accounts",
				"Invites" => "AccountInvite.AccountID_UserID.User:Invites"
			]
		]);
	}
}