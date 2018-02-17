<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\Model\Users;

use Rhubarb\Crown\DependencyInjection\Container;
use Rhubarb\Crown\Sendables\Email\EmailProvider;
use Rhubarb\Scaffolds\Saas\Landlord\Emails\InviteEmail;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Accounts\Account;
use Rhubarb\Stem\Exceptions\ModelConsistencyValidationException;
use Rhubarb\Stem\Filters\AndGroup;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Schema\Columns\BooleanColumn;
use Rhubarb\Stem\Schema\Columns\DateTimeColumn;
use Rhubarb\Stem\Schema\Columns\ForeignKeyColumn;
use Rhubarb\Stem\Schema\Columns\StringColumn;
use Rhubarb\Stem\Schema\Columns\UUIDColumn;
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
 * @property bool $Revoked
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
            new UUIDColumn("InviteID"),
            new StringColumn("Email", 150),
            new ForeignKeyColumn("UserID"),
            new StringColumn("AccountID", 50),
            new BooleanColumn("Accepted", false),
            new BooleanColumn("Sent", false),
            new DateTimeColumn("SentDate"),
            new BooleanColumn("Revoked", false)
        );

        $schema->uniqueIdentifierColumnName = 'InviteID';

        return $schema;
    }

    public static function fromEmailAndAccountID($email, $accountId)
    {
        return self::findFirst( new AndGroup(
            [
                new Equals("Email", $email),
                new Equals("AccountID", $accountId)
            ]
        ));
    }

    public function getUserUUID()
    {
        return $this->User->UUID;
    }

    public function accept()
    {
        $this->Account->attachUser($this->User);

        $this->User->Enabled = true;
        $this->User->save();

        $this->Accepted = true;
        $this->save();
    }

    public function send($resend = false)
    {
        if (!$this->Sent || $resend) {
            $inviteEmail = Container::instance(InviteEmail::class,$this);
            EmailProvider::selectProviderAndSend($inviteEmail);
            $this->SentDate = new \DateTime();
            $this->Sent = true;
            $this->save();
        }
    }

}