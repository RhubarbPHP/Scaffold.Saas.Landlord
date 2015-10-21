<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\Model\Users;

use Rhubarb\Scaffolds\Saas\Landlord\Emails\InviteEmail;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Accounts\Account;
use Rhubarb\Stem\Exceptions\ModelConsistencyValidationException;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Schema\Columns\Boolean;
use Rhubarb\Stem\Schema\Columns\DateTime;
use Rhubarb\Stem\Schema\Columns\ForeignKey;
use Rhubarb\Stem\Schema\Columns\String;
use Rhubarb\Stem\Schema\Columns\UUID;
use Rhubarb\Stem\Schema\ModelSchema;

/**
 * Class Invite
 * @package Rhubarb\Scaffolds\Saas\Landlord\Model\Users
 *
 * @property string $InviteID
 * @property string $Email
 * @property int $UserID
 * @property int $AccountID
 * @property bool $Accepted
 * @property bool $Sent
 * @property \DateTime $SentDate
 *
 * @property User $User
 * @property Account $Account
 *
 */
class Invite extends Model
{
    /**
     * Returns the schema for this data object.
     *
     * @return \Rhubarb\Stem\Schema\ModelSchema
     */
    protected function createSchema()
    {
        $schema = new ModelSchema("tblInvite");
        $schema->addColumn(
            new UUID("InviteID"),
            new String("Email", 150),
            new ForeignKey("UserID"),
            new ForeignKey("AccountID"),
            new Boolean("Accepted", false),
            new Boolean("Sent", false),
            new DateTime("SentDate")
        );

        $schema->uniqueIdentifierColumnName = 'InviteID';

        return $schema;
    }

    public function getUserUUID()
    {
        return $this->User->UUID;
    }

    public function setAccepted($value)
    {
        if (!$value && isset($this->modelData['Accepted']) && $this->modelData['Accepted']) {
            throw new ModelConsistencyValidationException();
        } else {
            $this->modelData['Accepted'] = $value;
        }
    }

    public function send()
    {
        if (!$this->Sent) {
            $inviteEmail = new InviteEmail($this);
            $inviteEmail->send();
            $this->SentDate = new \DateTime();
            $this->Sent = true;
            $this->save();
        }
    }

}