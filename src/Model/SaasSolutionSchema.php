<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\Model;

use Rhubarb\Stem\Schema\SolutionSchema;

class SaasSolutionSchema extends SolutionSchema
{
    public function __construct()
    {
        parent::__construct(0.1);

        $this->addModel("Account", __NAMESPACE__ . '\Accounts\Account');
        $this->addModel("AccountUser", __NAMESPACE__ . '\Accounts\AccountUser');
        $this->addModel("AccountInvite", __NAMESPACE__ . '\Accounts\AccountInvite');
        $this->addModel("Server", __NAMESPACE__ . '\Infrastructure\Server');
    }

    protected function defineRelationships()
    {
        parent::defineRelationships();

        $this->declareManyToManyRelationships(
            [
                "Account" =>
                    [
                        "Users" => "AccountUser.AccountID_UserID.User:Accounts",
                        "Invites" => "AccountInvite.AccountID_UserID.User:Invites"
                    ]
            ]);
    }
}