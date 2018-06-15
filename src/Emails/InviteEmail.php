<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\Emails;

use Rhubarb\Crown\DependencyInjection\Container;
use Rhubarb\Crown\Sendables\Email\Email;
use Rhubarb\Crown\Settings\WebsiteSettings;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\Invite;
use Rhubarb\Scaffolds\Saas\Landlord\Settings\LandlordSettings;

class InviteEmail extends Email
{
    protected $invite;

    public function __construct($invite)
    {
        $this->invite = $invite;

        $this->addRecipientByEmail($invite->Email);
    }

    /**
     * Get's the invite for this email.
     *
     * @return Invite
     */
    public function getInvite()
    {
        return $this->invite;
    }

    public function getText()
    {
        return "You've been invited to join us!";
    }

    public function getHtml()
    {
        $landlordSettings = LandlordSettings::singleton();

        $rd = base64_encode("/app/accounts/?i={$this->invite->_id}");

        return <<<END
<p>You've been invited to join us!</p>
<p><a href="{$landlordSettings->publicWebsiteUrl}login/?rd={$rd}&amp;i={$this->invite->_id}">Accept the invitation</a></p>
END;

    }

    public function getSubject()
    {
        return 'Welcome';
    }

    /**
     * Expresses the sendable as an array allowing it to be serialised, stored and recovered later.
     *
     * @return array
     */
    public function toArray()
    {
        return ["Invite" => $this->invite];
    }

    public static function fromArray($array)
    {
        $invite = $array["Invite"];
        return Container::instance(InviteEmail::class,(object)$invite);
    }
}