<?php

namespace SuttonBaker\Impresario\Model\Db;

use \SuttonBaker\Impresario\Definition\Variation as VariationDefinition;

/**
 * Class Variation
 * @package SuttonBaker\Impresario\Model\Db
 */
class Variation extends Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'variation';
        $this->idColumn = 'variation_id';

        return $this;
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->getStatus() == VariationDefinition::STATUS_APPROVED;
    }

    /**
     * @return float
     */
    public function getProfit()
    {
        return (float) $this->getValue() - (float) $this->getNetCost();
    }

    /**
     * @return float|int
     */
    public function getGp()
    {
        if ($this->getValue() !== 0) {
            return round(($this->getProfit() / $this->getValue()) * 100, 2);
        }

        return 0;
    }

    /**
     * @return null|Project
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getProject()
    {
        if (!$this->getId()) {
            return null;
        }

        return $this->getProjectHelper()->getProject($this->getProjectId());
    }

    /**
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function afterSave()
    {
        // Update the project's values
        if ($project = $this->getProject()) {
            $project->save();
        }
    }
}
