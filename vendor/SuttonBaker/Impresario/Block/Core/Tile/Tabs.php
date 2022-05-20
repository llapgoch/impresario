<?php
namespace SuttonBaker\Impresario\Block\Core\Tile;

use DaveBaker\Core\Block\BlockInterface;

/**
 * Class Tabs
 * @package SuttonBaker\Impresario\Block\Core\Tile
 */
class Tabs
    extends \DaveBaker\Core\Block\Html\Base
    implements BlockInterface
{
    const TABS_DATA_KEY = 'tabs';

    /**
     * @return \SuttonBaker\Impresario\Block\Base|void
     */
    public function init()
    {
        parent::init();
        $this->setTemplate('core/tile-tabs.phtml');
    }

    /**
     * @return array|mixed|null
     */
    public function getTabs()
    {
        if(!$this->getData(self::TABS_DATA_KEY)){
            $this->setData(self::TABS_DATA_KEY, []);
        }

        return $this->getData(self::TABS_DATA_KEY);
    }

    /**
     * @param $tab
     * @return array
     */
    public function getAnchorClass($tab)
    {
        $classes = [];

        if(isset($tab['active']) && $tab['active'] == true){
            $classes[] = 'active';
        }

        if(isset($tab['disabled']) && $tab['disabled'] == true){
            $classes[] = 'disabled';
        }

        return implode(" ", $classes);
    }

    /**
     *
     * @param array $tab
     * @return string
     */
    public function getNewWindowAttr($tab)
    {
        if(isset($tab['new_window']) && (bool) $tab['new_window']) {
            return 'target="_blank"';
        }

        return '';
    }

}