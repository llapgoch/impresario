<?php

namespace SuttonBaker\Impresario\Layout;
/**
 * Class Base
 * @package SuttonBaker\Impresario\Layout
 */
abstract class Base extends \DaveBaker\Core\Layout\Base
{
    /** @var string  */
    protected $blockPrefix = '';

    /**
     * @return string
     */
    protected function getBlockPrefix()
    {
        return $this->blockPrefix;
    }
}
