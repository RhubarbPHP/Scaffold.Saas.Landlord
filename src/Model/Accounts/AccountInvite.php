<?php

namespace Rhubarb\Crown\Scaffolds\Saas\Model\Accounts;

use Rhubarb\Crown\Modelling\Models\Model;
use Rhubarb\Crown\Modelling\Repositories\MySql\Schema\Columns\AutoIncrement;
use Rhubarb\Crown\Modelling\Repositories\MySql\Schema\Columns\DateTime;
use Rhubarb\Crown\Modelling\Repositories\MySql\Schema\Columns\ForeignKey;
use Rhubarb\Crown\Modelling\Repositories\MySql\Schema\Columns\Varchar;
use Rhubarb\Crown\Modelling\Repositories\MySql\Schema\MySqlSchema;

class AccountInvite extends Model
{
	public function CreateSchema()
	{
		$schema = new MySqlSchema( "tblAccountInvite" );
		$schema->AddColumn(
			new AutoIncrement( "AccountInviteID" ),
			new ForeignKey( "AccountID" ),
			new ForeignKey( "UserID" ),
			new DateTime( "ExpiryDate" )
		);

		return $schema;
	}
}