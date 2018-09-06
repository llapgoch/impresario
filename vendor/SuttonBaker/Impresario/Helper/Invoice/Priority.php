<?php

namespace SuttonBaker\Impresario\Helper\OutputProcessor\Task;

/**
 * Class Type
 * @package SuttonBaker\Impresario\Helper\OutputProcessor\Task
 */
class Priority
    extends \SuttonBaker\Impresario\Helper\OutputProcessor\Base
    implements \DaveBaker\Core\Helper\OutputProcessor\OutputProcessorInterface
{
    /**
     * @param $value
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function process($value)
    {
        return $this->getTaskHelper()->getPriorityDisplayName($value);
    }
}