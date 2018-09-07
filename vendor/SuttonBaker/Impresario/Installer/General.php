<?php

namespace SuttonBaker\Impresario\Installer;

use \SuttonBaker\Impresario\Definition\Roles;
/**
 * Class Client
 * @package SuttonBaker\Impresario\Installer\
 */
class General
    extends \DaveBaker\Core\Installer\Base
    implements \DaveBaker\Core\Installer\InstallerInterface
{
    protected $installerCode = 'impresario_general';

    /**
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function install()
    {

        $roles = Roles::getRoles();
        $userHelper = $this->getUserHelper();

        $userHelper->addRole(
            Roles::ROLE_ADMINISTRATOR,
            Roles::getRoleName(Roles::ROLE_ADMINISTRATOR),
            Roles::getCapabilities(),
            false
        );
        $userHelper->addRole(
            Roles::ROLE_PROJECT_MANAGER,
            Roles::getRoleName(Roles::ROLE_PROJECT_MANAGER),
            Roles::getCapabilities()
        );

        $userHelper->addRole(
            Roles::ROLE_ENGINEER,
            Roles::getRoleName(Roles::ROLE_ENGINEER),
            [Roles::CAP_VIEW_ENQUIRY, Roles::CAP_EDIT_ENQUIRY, Roles::CAP_EDIT_TASK, Roles::CAP_VIEW_TASK]
        );

        $userHelper->addRole(Roles::ROLE_ESTIMATOR,
            Roles::getRoleName(Roles::ROLE_ESTIMATOR), [
                Roles::CAP_VIEW_PROJECT,
                Roles::CAP_EDIT_PROJECT,
                Roles::CAP_VIEW_INVOICE,
                Roles::CAP_EDIT_INVOICE,
                Roles::CAP_VIEW_VARIATION,
                Roles::CAP_EDIT_VARIATION,
                Roles::CAP_EDIT_QUOTE,
                Roles::CAP_VIEW_QUOTE,
                Roles::CAP_EDIT_TASK,
                Roles::CAP_VIEW_TASK
            ]
        );

        $userHelper->addRole(Roles::ROLE_FOREMAN,
            Roles::getRoleName(Roles::ROLE_FOREMAN), [
                Roles::CAP_EDIT_PROJECT,
                Roles::CAP_VIEW_PROJECT,
                Roles::CAP_EDIT_TASK,
                Roles::CAP_VIEW_TASK
            ]
        );

    }
}