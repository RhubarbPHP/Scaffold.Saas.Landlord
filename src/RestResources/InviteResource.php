<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\RestResources;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\RestApi\Exceptions\RestImplementationException;
use Rhubarb\RestApi\Resources\ModelRestResource;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Accounts\Account;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\Invite;
use Rhubarb\Stem\Collections\Collection;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;
use Rhubarb\Stem\Filters\Equals;

class InviteResource extends ModelRestResource
{
    /**
     * Returns the name of the model to use for this resource.
     *
     * @return string
     */
    public function getModelName()
    {
        return "Invite";
    }

    public function post($restResource)
    {
        // We only should create an invite if there isn't one already pending for this account.
        try {
            $invite = Invite::fromEmailAndAccountID($restResource["Email"], $restResource["AccountID"]);

            if (!$invite->Accepted){
                // The invitation is still valid - we can just return this one after asking it to resend
                // the invitation email.
                $this->setModel($invite);
                $invite->send(true);
                return parent::get();
            }
        } catch (RecordNotFoundException $er){
        }

        // See if the account already has a user with this email address.
        $account = new Account($restResource["AccountID"]);
        $users = $account->Users;
        $users->filter(new Equals("Email", $restResource["Email"]));

        if (count($users)){
            throw new RestImplementationException("There is already a user with this email address connected to the account.");
        }

        return parent::post($restResource);
    }

    /**
     * Override to filter a model collection to apply any necessary filters only when this is the REST collection of the specific resource being fetched
     *
     * @param Collection $collection
     */
    public function filterModelCollectionAsContainer(Collection $collection)
    {
        parent::filterModelCollectionAsContainer($collection);

        $collection->filter(new Equals("Accepted", false));
    }

    protected function getColumns()
    {
        $columns = parent::getColumns();
        $columns[] = 'UserUUID';
        $columns[] = 'AccountID';
        $columns[] = 'Accepted';
        $columns[] = 'Email';

        return $columns;
    }
}