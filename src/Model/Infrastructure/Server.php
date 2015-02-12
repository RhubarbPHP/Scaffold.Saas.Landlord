<?php

namespace Rhubarb\Crown\Scaffolds\Saas\Model\Infrastructure;

use Rhubarb\Crown\Modelling\Models\Model;
use Rhubarb\Crown\Modelling\Repositories\MySql\Schema\Columns\AutoIncrement;
use Rhubarb\Crown\Modelling\Repositories\MySql\Schema\Columns\EncryptedVarchar;
use Rhubarb\Crown\Modelling\Repositories\MySql\Schema\Columns\Varchar;
use Rhubarb\Crown\Modelling\Repositories\MySql\Schema\MySqlSchema;

class Server extends Model
{
	/**
	 * Returns the schema for this data object.
	 *
	 * @return \Rhubarb\Crown\Modelling\Schema\ModelSchema
	 */
	public function CreateSchema()
	{
		$schema = new MySqlSchema( "tblServer" );
		$schema->AddColumn(
			new AutoIncrement( "ServerID" ),
			new Varchar( "ServerName", 50 ),
			new EncryptedVarchar( "Host", 40 ),
			new EncryptedVarchar( "Port", 10 )
		);

		return $schema;
	}
}