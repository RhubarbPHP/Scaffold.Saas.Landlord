<?php

namespace Rhubarb\Crown\Scaffolds\Saas\Model\Accounts;

use Rhubarb\Crown\Modelling\Models\Model;
use Rhubarb\Crown\Modelling\Repositories\MySql\Schema\Columns\AutoIncrement;
use Rhubarb\Crown\Modelling\Repositories\MySql\Schema\Columns\ForeignKey;
use Rhubarb\Crown\Modelling\Repositories\MySql\Schema\Columns\Varchar;
use Rhubarb\Crown\Modelling\Repositories\MySql\Schema\MySqlSchema;

class AccountUser extends Model
{
	public function CreateSchema()
	{
		$schema = new MySqlSchema( "tblAccountUser" );

		$schema->AddColumn(
			new AutoIncrement( "AccountUserID" ),
			new ForeignKey( "AccountID" ),
			new ForeignKey( "UserID" )
		);

		return $schema;
	}
}