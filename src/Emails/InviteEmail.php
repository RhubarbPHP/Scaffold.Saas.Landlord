<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\Emails;

use Rhubarb\Crown\Email\TemplateEmail;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\Invite;

class InviteEmail extends TemplateEmail
{
    protected $invite;

    public function __construct( Invite $invite )
    {
        $this->invite = $invite;

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
        return 'welcome, jerk';
    }

    protected function getHtmlTemplateBody()
    {
        return 'welcome, jerk';
    }

    protected function getSubjectTemplate()
    {
        return 'welcome, jerk';
    }
}