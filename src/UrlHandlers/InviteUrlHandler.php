<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\UrlHandlers;

use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\RestApi\UrlHandlers\UnauthenticatedRestResourceHandler;
use Rhubarb\Scaffolds\Saas\Landlord\Model\Users\Invite;

class InviteUrlHandler extends UnauthenticatedRestResourceHandler
{
    public function getRestResource()
    {
        $resource = parent::getRestResource();

        /**
         * @var WebRequest $request
         */
        $request = Request::current();

        $parts = explode("/", $request->urlPath);
        $inviteId = $parts[count($parts) - 1];

        $resource->setModel(new Invite($inviteId));

        return $resource;
    }

}
