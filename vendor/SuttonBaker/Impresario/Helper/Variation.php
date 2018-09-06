<?php

namespace SuttonBaker\Impresario\Helper;

use \SuttonBaker\Impresario\Definition\Variation as VariationDefinition;
/**
 * Class Variation
 * @package SuttonBaker\Impresario\Helper
 */
class Variation extends Base
{

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Variation\Collection
     * @throws \DaveBaker\Core\Object\Exception
     *
     * Returns a collection of non-deleted variations
     */
    public function getVariationCollection()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Variation\Collection $collection */
        $collection = $this->createAppObject(
            '\SuttonBaker\Impresario\Model\Db\Variation\Collection'
        )->where('is_deleted=?', '0');

        /** @var \Zend_Db_Select $select */
        $select = $collection->getSelect();
        $userTable = $this->getApp()->getHelper('Db')->getTableName('users', false);


        $collection->joinLeft(
            ['created_by_user'=> $userTable],
            "created_by_user.ID={{variation}}.created_by_id",
            ['created_by_name' => 'user_login']
        )->order('{{variation}}.variation_id DESC');


        return $collection;
    }

    /**
     * @param $projectId
     * @return \SuttonBaker\Impresario\Model\Db\Variation\Collection
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getVariationCollectionForProject($projectId)
    {
        return $this->getVariationCollection()
            ->where('project_id=?', $projectId);
    }

    /**
     * @param $variationId
     * @return \SuttonBaker\Impresario\Model\Db\Variation
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getVariation($variationId = null)
    {
        $variation = $this->createAppObject(VariationDefinition::DEFINITION_MODEL);

        if($variationId){
            $variation->load($variationId);
        }

        return $variation;
    }

}