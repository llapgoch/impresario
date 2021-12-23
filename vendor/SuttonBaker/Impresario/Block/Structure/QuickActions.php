<?php

namespace SuttonBaker\Impresario\Block\Structure;

use DaveBaker\Core\Block\Template;

/**
 * Class QuickActions
 * @package SuttonBaker\Impresario\Block\Structure
 */
class QuickActions
    extends Template
{
    protected $avatarSize = 45;
    /**
     * @return Template
     */
    public function init()
    {
        parent::init();
        $this->setTemplate('nav/quick-actions.phtml');
    }

    /**
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getUsername()
    {
        if($user = $this->getUserHelper()->getCurrentUser()){
            return $user->getDisplayName();
        }
    }

    /**
     * @return bool|mixed|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getAvatar()
    {
        return get_avatar($this->getUserHelper()->getCurrentUserId(), $this->avatarSize);
    }

    /**
     * @return string
     */
    public function getLogoutUrl()
    {
        return wp_logout_url();
    }
}