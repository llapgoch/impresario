<?php

namespace SuttonBaker\Impresario\Helper;

use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class Role
 * @package SuttonBaker\Impresario\Helper
 */
class Role extends Base
{
    public function getCustomerServiceUsers()
    {
        return $this->getUserHelper()->getUsersForRole(Roles::ROLE_CUSTOMER_SERVICES);
    }
    /**
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getEngineers()
    {
        return $this->getUserHelper()->getUsersForRole(Roles::ROLE_ENGINEER);
    }

    /**
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getForemen()
    {
        return $this->getUserHelper()->getUsersForRole(Roles::ROLE_FOREMAN);
    }

    /**
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getProjectManagers()
    {
        return $this->getUserHelper()->getUsersForRole(Roles::ROLE_PROJECT_MANAGER);
    }

    /**
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getAdministrators()
    {
        return $this->getUserHelper()->getUsersForRole(Roles::ROLE_ADMINISTRATOR);
    }

    /**
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getEstimators()
    {
        return $this->getUserHelper()->getUsersForRole(Roles::ROLE_ESTIMATOR);
    }

}