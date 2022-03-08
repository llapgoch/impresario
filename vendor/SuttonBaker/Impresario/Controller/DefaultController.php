<?php

namespace SuttonBaker\Impresario\Controller;

use SuttonBaker\Impresario\Definition\Enquiry;

/**
 * Class DefaultController
 * @package SuttonBaker\Impresario\Controller
 */
class DefaultController
extends \SuttonBaker\Impresario\Controller\Base
implements \DaveBaker\Core\Controller\ControllerInterface
{
    protected $allowedUris = [
        "^\/oauth\/.*"
    ];
    // Add this to the default handle so all pages require a user to be logged in

    /**
     * @return Base|void
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function preDispatch()
    {
        if (!$this->getRequest()->isAjax() || $this->getRequest()->isRest()) {
            $this->requiresLogin = true;
        }

        $url = $this->getUrlHelper()->getCurrentUrl();
        $urlParts = parse_url($url);

        $path = $urlParts['path'];
        $matched = false;

        foreach ($this->allowedUris as $uri) {
            if (preg_match("|" . $uri . "|", $path)) {
                $matched = true;
                break;
            }
        }

        if ($matched) {
            $this->requiresLogin = false;
        }


        /* Global scripts which can be registered in block scope */
        wp_register_script(
            'impresario_deleter',
            get_template_directory_uri() . '/assets/js/deleter.widget.js',
            ['jquery', 'jquery-ui-widget']
        );

        wp_register_script(
            'impresario_record_monitor',
            get_template_directory_uri() . '/assets/js/record.monitor.widget.js',
            ['jquery', 'jquery-ui-widget']
        );

        wp_register_script(
            'impresario_serialize_object',
            get_template_directory_uri() . '/assets/js/jquery.serialize-object.min.js',
            ['jquery']
        );

        parent::preDispatch();
    }

    public function execute()
    {
    }
}
