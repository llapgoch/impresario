<?php

namespace SuttonBaker\Impresario\Event;
/**
 * Class GlobalEvents
 * @package SuttonBaker\Impresario\Event
 */
class GlobalEvents extends \DaveBaker\Core\Base
{
    public function _construct()
    {
        $this->addEvents();
    }

    /**
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function addEvents()
    {
        $this->addEvent('body_class', function($classes){
            $classes[] = 'bg-1';
            return $classes;
        });

    }
}