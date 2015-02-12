<?php

namespace Rhubarb\Crown\Scaffolds\Saas\Model\Accounts;

use Rhubarb\Crown\Modelling\Models\Model;
use Rhubarb\Crown\Modelling\Repositories\MySql\Schema\Columns\AutoIncrement;
use Rhubarb\Crown\Modelling\Repositories\MySql\Schema\Columns\Varchar;
use Rhubarb\Crown\Modelling\Repositories\MySql\Schema\MySqlSchema;
use Rhubarb\Crown\Scaffolds\Authentication\User;

class Account extends Model
{
	public function CreateSchema()
	{
		$schema = new MySqlSchema( "tblAccount" );
		$schema->AddColumn(
			new AutoIncrement( "AccountID" ),
			new Varchar( "AccountName", 50 )
		);

		$schema->labelColumnName = "AccountName";

		return $schema;
	}

	public function Invite( User $user )
	{
		$this->Invites->Append( $user );
	}
}