<?php

namespace SuttonBaker\Impresario;

use \SuttonBaker\Impresario\Definition\Client as ClientDefinition;
use SuttonBaker\Impresario\Definition\Page;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class Main
 * @package SuttonBaker\Impresario
 */
class Main
extends \DaveBaker\Core\Main\Base
implements \DaveBaker\Core\Main\MainInterface
{



    /**
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function init()
    {
        $this->createAppObject('\SuttonBaker\Impresario\Event\GlobalEvents');
        $this->createAppObject('\SuttonBaker\Impresario\Event\Upload');
        $this->createAppObject('\SuttonBaker\Impresario\Event\LoginEvents');



        // Test OAuth

        add_filter('wo_endpoints', [$this, 'wo_extend_resource_api'], 2);
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Project
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getProjectHelper()
    {
        return $this->createAppObject(\SuttonBaker\Impresario\Helper\Project::class);
    }


    /**
     * @return \SuttonBaker\Impresario\Helper\Client
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getCllientHelper()
    {
        return $this->createAppObject(\SuttonBaker\Impresario\Helper\Client::class);
    }

    function wo_extend_resource_api($methods)
    {
        $methods['allprojects'] = array('func' => function () {
            $projects = $this->getProjectHelper()->getBaseProjectCollection()->load();

            $projectOutput = [];

            foreach ($projects as $project) {
                $projectOutput[] = [
                    'projectName' => $project->getProjectName(),
                    'clientId' => $project->getClientId(),
                    'isDeleted' => $project->getIsDeleted(),
                    'tandimoId' => $project->getId()
                ];
            }

            $response = new \WPOAuth2\Response($projectOutput);
            $response->send();
            exit;
        });

        $methods['allclients'] = array('func' => function () {
            $clients = $this->getCllientHelper()->getBaseClientCollection()->load();
            $clientOutput = [];

            foreach ($clients as $client) {
                $clientOutput[] = [
                    'clientName' => $client->getClientName(),
                    'tandimoId' => $client->getId(),
                    'isDeleted' => $client->getIsDeleted()
                ];
            }

            $response = new \WPOAuth2\Response($clientOutput);
            $response->send();
            exit;
        });


        // Get all users to be consumed by QHSE
        $methods['allusers'] = array('func' => function () {
            global $wpdb;
            $prefix = $wpdb->prefix;

            $userTable = $this->getUserHelper()->getUserTableName();
            $userMetaTable = $this->getUserHelper()->getUserMetaTableName();
            $users = $this->getUserHelper()->getUserCollection();
            $allRoles = $this->getOptionManager()->get($prefix . 'user_roles');

            // Join on the user meta table to get all roles for the user. These are roles, not capabilities
            $users->getSelect()->join(
                $this->getUserHelper()->getUserMetaTableName(),
                "{$userMetaTable}.user_id={$userTable}.ID AND meta_key='{$prefix}capabilities'",
                ['capabilities' => 'meta_value']
            );

            $userResults = $users->load();
            $data = [];

            foreach ($userResults as $user) {
                // $data[$user->getID()]  = $user->getData();
                $userCapabilities = [];
                $userRoles = unserialize($user->getCapabilities());

                // Get all of the capabilites for the role, look up the capabilities and merge them together
                foreach ($userRoles as $key => $userRole) {
                    if (isset($allRoles[$key]['capabilities'])) {
                        $userCapabilities = array_merge($userCapabilities, $allRoles[$key]['capabilities']);
                    }
                }

                $data[$user->getID()] = [
                    'ID' => $user->getID(),
                    'user_login' => $user->getUserLogin(),
                    'user_email' => $user->getUserEmail(),
                    'display_name' => $user->getDisplayName(),
                    'capabilities' => $userCapabilities
                ];
            }

            $response = new \WPOAuth2\Response($data);
            $response->send();
            exit;
        });
        return $methods;
    }

    /**
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function registerApiActions()
    {
        $api = $this->getApp()->getApiManager();

        $api->addRoute(
            'client',
            '\SuttonBaker\Impresario\Api\Client'
        );

        $api->addRoute(
            'enquiry',
            '\SuttonBaker\Impresario\Api\Enquiry'
        );

        $api->addRoute(
            'quote',
            '\SuttonBaker\Impresario\Api\Quote'
        );

        $api->addRoute(
            'task',
            '\SuttonBaker\Impresario\Api\Task'
        );

        $api->addRoute(
            'project',
            '\SuttonBaker\Impresario\Api\Project'
        );

        $api->addRoute(
            'invoice',
            '\SuttonBaker\Impresario\Api\Invoice'
        );

        $api->addRoute(
            'cost',
            '\SuttonBaker\Impresario\Api\Cost'
        );

        $api->addRoute(
            'supplier',
            '\SuttonBaker\Impresario\Api\Supplier'
        );

        $api->addRoute(
            'variation',
            '\SuttonBaker\Impresario\Api\Variation'
        );

        $api->addRoute(
            'archive',
            '\SuttonBaker\Impresario\Api\Archive'
        );

        $api->addRoute(
            'upload',
            '\SuttonBaker\Impresario\Api\Upload'
        );
    }

    /**
     * @throws \DaveBaker\Core\Controller\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function registerControllers()
    {
        $this->getApp()->getContollerManager()->register([
            \SuttonBaker\Impresario\Definition\Page::CLIENT_EDIT => '\SuttonBaker\Impresario\Controller\Client\EditController',
            \SuttonBaker\Impresario\Definition\Page::CLIENT_LIST => '\SuttonBaker\Impresario\Controller\Client\ListController',
            \SuttonBaker\Impresario\Definition\Page::SUPPLIER_LIST => '\SuttonBaker\Impresario\Controller\Supplier\ListController',
            \SuttonBaker\Impresario\Definition\Page::SUPPLIER_EDIT => '\SuttonBaker\Impresario\Controller\Supplier\EditController',
            \SuttonBaker\Impresario\Definition\Page::ENQUIRY_EDIT => '\SuttonBaker\Impresario\Controller\Enquiry\EditController',
            \SuttonBaker\Impresario\Definition\Page::ENQUIRY_LIST => '\SuttonBaker\Impresario\Controller\Enquiry\ListController',
            \SuttonBaker\Impresario\Definition\Page::ENQUIRY_REPORT_DOWNLOAD => '\SuttonBaker\Impresario\Controller\Enquiry\ReportController',
            \SuttonBaker\Impresario\Definition\Page::TASK_EDIT => '\SuttonBaker\Impresario\Controller\Task\EditController',
            \SuttonBaker\Impresario\Definition\Page::TASK_LIST => '\SuttonBaker\Impresario\Controller\Task\ListController',
            \SuttonBaker\Impresario\Definition\Page::QUOTE_EDIT => '\SuttonBaker\Impresario\Controller\Quote\EditController',
            \SuttonBaker\Impresario\Definition\Page::QUOTE_LIST => '\SuttonBaker\Impresario\Controller\Quote\ListController',
            \SuttonBaker\Impresario\Definition\Page::QUOTE_REPORT_DOWNLOAD => '\SuttonBaker\Impresario\Controller\Quote\ReportController',
            \SuttonBaker\Impresario\Definition\Page::PROJECT_EDIT => '\SuttonBaker\Impresario\Controller\Project\EditController',
            \SuttonBaker\Impresario\Definition\Page::PROJECT_LIST => '\SuttonBaker\Impresario\Controller\Project\ListController',
            \SuttonBaker\Impresario\Definition\Page::PROJECT_REPORT_DOWNLOAD => '\SuttonBaker\Impresario\Controller\Project\ReportController',
            \SuttonBaker\Impresario\Definition\Page::INVOICE_EDIT => '\SuttonBaker\Impresario\Controller\Invoice\EditController',
            \SuttonBaker\Impresario\Definition\Page::COST_EDIT => '\SuttonBaker\Impresario\Controller\Cost\EditController',
            \SuttonBaker\Impresario\Definition\Page::VARIATION_EDIT => '\SuttonBaker\Impresario\Controller\Variation\EditController',
            \SuttonBaker\Impresario\Definition\Page::ARCHIVE_REPORT_DOWNLOAD => '\SuttonBaker\Impresario\Controller\Archive\ReportController',
            \DaveBaker\Core\Layout\Handle\Manager::HANDLE_DEFAULT => '\SuttonBaker\Impresario\Controller\DefaultController'
        ]);
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function registerInstallers()
    {
        $this->getApp()->getInstallerManager()->register([
            '\SuttonBaker\Impresario\Installer\Client',
            '\SuttonBaker\Impresario\Installer\Supplier',
            '\SuttonBaker\Impresario\Installer\Enquiry',
            '\SuttonBaker\Impresario\Installer\Task',
            '\SuttonBaker\Impresario\Installer\Quote',
            '\SuttonBaker\Impresario\Installer\Cost',
            '\SuttonBaker\Impresario\Installer\Project',
            '\SuttonBaker\Impresario\Installer\InvoiceVariation',
            '\SuttonBaker\Impresario\Installer\Archive',
            '\SuttonBaker\Impresario\Installer\General'
        ]);
    }

    /**
     * @throws \DaveBaker\Core\Layout\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function registerLayouts()
    {
        $this->getApp()->getLayoutManager()->register([
            '\SuttonBaker\Impresario\Layout\DefaultLayout',
            '\SuttonBaker\Impresario\Layout\IndexLayout',
            '\SuttonBaker\Impresario\Layout\Client',
            '\SuttonBaker\Impresario\Layout\Supplier',
            '\SuttonBaker\Impresario\Layout\Enquiry',
            '\SuttonBaker\Impresario\Layout\Task',
            '\SuttonBaker\Impresario\Layout\Quote',
            '\SuttonBaker\Impresario\Layout\Project',
            '\SuttonBaker\Impresario\Layout\Invoice',
            '\SuttonBaker\Impresario\Layout\Cost',
            '\SuttonBaker\Impresario\Layout\Variation',
            '\SuttonBaker\Impresario\Layout\Archive',
            '\SuttonBaker\Impresario\Layout\Login'
        ]);
    }
}
