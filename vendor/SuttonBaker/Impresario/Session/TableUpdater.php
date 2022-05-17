<?php

namespace SuttonBaker\Impresario\Session;

use DaveBaker\Core\Session\Base;
use DaveBaker\Core\Session\SessionInterface;

/**
 * Class TableUpdater
 * @package SuttonBaker\Impresario\Session
 */
class TableUpdater
    extends Base
    implements SessionInterface
{
    protected $sessionNamespace = 'table_updater';
}