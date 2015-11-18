<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\Emails;

use Rhubarb\Crown\Email\TemplateEmail;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\Invite;
use Rhubarb\Scaffolds\Saas\Landlord\Settings\LandlordSettings;

class InviteEmail extends TemplateEmail
{
    protected $invite;

    public function __construct( Invite $invite )
    {
        $this->invite = $invite;
        $this->addRecipient($invite->Email);

        parent::__construct();
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

    protected function getTextTemplateBody()
    {
        return "You've been invited to join us!";
    }

    protected function getHtmlTemplateBody()
    {
        $landlordSettings = new LandlordSettings();

        $rd = base64_encode("/app/accounts/");

        return <<<END
<p>You've been invited to join us!</p>
<p><a href="{$landlordSettings->PublicWebsiteUrl}login/?rd={$rd}&amp;i={$this->invite->InviteID}">Accept the invitation</a></p>
END;

    }

    protected function getSubjectTemplate()
    {
        return 'welcome, jerk';
    }
}