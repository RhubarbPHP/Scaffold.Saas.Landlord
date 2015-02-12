<?php

namespace Rhubarb\Scaffolds\Saas\Model\Accounts;

use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\AutoIncrement;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\DateTime;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\ForeignKey;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\Varchar;
use Rhubarb\Stem\Repositories\MySql\Schema\MySqlSchema;

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