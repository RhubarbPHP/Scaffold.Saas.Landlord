<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\Model\Users;

use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Schema\Columns\AutoIncrement;
use Rhubarb\Stem\Schema\Columns\ForeignKey;
use Rhubarb\Stem\Schema\Columns\String;
use Rhubarb\Stem\Schema\Columns\UUID;
use Rhubarb\Stem\Schema\ModelSchema;

class Invite extends Model
{
    /**
     * Returns the schema for this data object.
     *
     * @return \Rhubarb\Stem\Schema\ModelSchema
     */
    protected function createSchema()
    {
        $schema = new ModelSchema( "tblInvite" );
        $schema->addColumn(
            new UUID( "InviteID" ),
            new String( "Email", 150 ),
            new ForeignKey( "UserID" ),
            new ForeignKey( "AccountID" )
        );

        $schema->uniqueIdentifierColumnName = 'InviteID';

        return $schema;
    }

    public function getUserUUID()
    {
        return $this->User->UUID;
    }


}