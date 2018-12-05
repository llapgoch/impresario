<?php

namespace SuttonBaker\Impresario\Config;
/**
 * Class Element
 * @package SuttonBaker\Impresario\Config
 */
class Element extends \DaveBaker\Core\Config\Element
{
    /**
     * Installer constructor
     */
    public function __construct()
    {
        $this->mergeConfig([
            'elementClasses' => [
                'main-heading' => 'pageheader col-12 text-center',
                'input' => 'form-control',
                'input-submit' => 'btn btn-primary',
                'select' => 'form-control',
                'textarea' => 'form-control',
                'button' => 'btn btn-primary',
                'input-file' => 'form-control-file',
                'tile' => 'tile',
                'tile-white' => 'color transparent-white',
                'tile-black' => 'color transparent-black',
                'form' => '',
                'label' => 'control-label',
                'form-group' => 'form-group',
                'form-row' => 'form-row',
                'button-anchor' => 'btn btn-primary',
                'form-error-message' => 'alert alert-danger',
                'table' => 'table',
                'table-status-rows' => 'table-status-rows',
                'paginator' => 'pagination pagination-xl nomargin pagination-custom justify-content-center',
                'file-uploader-component-label' => 'btn btn-sm btn-primary'
            ],
            'elementAttributes' => [
                'textarea' => ['rows' => 8]
            ],
            'hiddenClass' => 'd-none',
            'sortableTableJsClass' => 'js-table-updater',
        ]);

    }
}