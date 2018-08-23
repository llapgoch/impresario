<?php

namespace SuttonBaker\Impressario\WP\Model\Db;

class Quote extends \DaveBaker\Core\Model\Db\Base
{
    /**
     * @return $this
     */
    public function init()
    {
        $this->tableName = 'quote';
        $this->idColumn = 'id';

        return $this;
    }
}