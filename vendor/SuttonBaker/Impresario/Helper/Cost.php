<?php

namespace SuttonBaker\Impresario\Helper;

use Exception;
use \SuttonBaker\Impresario\Definition\Cost as CostDefinition;
use SuttonBaker\Impresario\Definition\Roles;
use SuttonBaker\Impresario\Model\Db\Cost\Item;

/**
 * Class Cost
 * @package SuttonBaker\Impresario\Helper
 */
class Cost extends Base
{
    /** @var array  */
    protected $editCapabilities = [Roles::CAP_ALL, Roles::CAP_EDIT_COST];
    protected $viewCapabilities = [Roles::CAP_ALL, Roles::CAP_EDIT_COST, Roles::CAP_VIEW_COST];

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Cost\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getCostCollection()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Cost\Collection $collection */
        $collection = $this->createAppObject(
            '\SuttonBaker\Impresario\Model\Db\Cost\Collection'
        )->where('{{cost}}.is_deleted=?', '0');

        $userTable = $this->getApp()->getHelper('Db')->getTableName('users', false);
        $supplierTable = $this->getApp()->getHelper('Db')->getTableName('supplier');

        $collection->joinLeft(
            ['supplier' => $supplierTable],
            "supplier.supplier_id={{cost}}.supplier_id",
            ['supplier_name' => 'supplier_name']
        );

        $collection->joinLeft(
            ['created_by_user' => $userTable],
            "created_by_user.ID={{cost}}.created_by_id",
            ['created_by_name' => 'display_name']
        )->order('{{cost}}.cost_id DESC');

        return $collection;
    }

    /**
     * Get cost invoice po items for a cost invoice (purchase order) id
     *
     * @param int $costInvoiceId
     * @return \SuttonBaker\Impresario\Model\Db\Cost\Item\Collection
     */
    public function getCostInvoiceItems($costInvoiceId)
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Cost\Item\Collection $collection */
        $collection = $this->createAppObject(
            \SuttonBaker\Impresario\Model\Db\Cost\Item\Collection::class
        )->where('{{cost_po_item}}.is_deleted=?', '0')
            ->where('{{cost_po_item}}.cost_id=?', $costInvoiceId);

        return $collection;
    }


    /**
     * @param $status
     * @return string
     */
    public function getCostInvoiceTypeName($status)
    {
        return $this->getDisplayName($status, CostDefinition::getCostInvoiceTypes());
    }


    /**
     * @param $entityId
     * @param $entityType
     * @return \SuttonBaker\Impresario\Model\Db\Cost\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getCostCollectionForEntity($entityId, $entityType)
    {
        return $this->getCostCollection()
            ->where('cost_type=?', $entityType)
            ->where('parent_id=?', $entityId);
    }


    /**
     * @param string $costType
     * @return mixed|string
     */
    public function getCostTypeDisplayName($costType)
    {
        return $this->getDisplayName($costType, CostDefinition::getCostTypes());
    }

    /**
     * @param $costType
     * @return bool
     */
    public function isValidCostType($costType)
    {
        return in_array($costType, array_keys(CostDefinition::getCostTypes()));
    }

    /**
     * @param $costId
     * @return \SuttonBaker\Impresario\Model\Db\Cost
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getCost($costId = null)
    {
        $cost = $this->createAppObject(CostDefinition::DEFINITION_MODEL);

        if ($costId) {
            $cost->load($costId);
        }

        return $cost;
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Cost $cost
     * @return null|\SuttonBaker\Impresario\Model\Db\Project
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getParentForCost(
        \SuttonBaker\Impresario\Model\Db\Cost $cost
    ) {
        if ($cost->getCostType() == CostDefinition::COST_TYPE_PROJECT) {
            return $this->getProjectHelper()->getProject($cost->getParentId());
        }

        return null;
    }

    /**
     * @param $parentInstance
     * @return string
     */
    public function getCostTypeForParent($parentInstance)
    {
        if ($parentInstance instanceof \SuttonBaker\Impresario\Model\Db\Project) {
            return CostDefinition::COST_TYPE_PROJECT;
        }

        return null;
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\OutputProcessor\Cost\Type
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getCostInvoiceTypeOutputProcessor()
    {
        return $this->createAppObject(\SuttonBaker\Impresario\Helper\OutputProcessor\Cost\InvoiceType::class);
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Cost $instance
     * @return mixed|string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function determineCostTypeName(
        \SuttonBaker\Impresario\Model\Db\Cost $instance
    ) {
        if ($instance->getId()) {
            return $this->getCostTypeDisplayName($instance->getCostType());
        }

        if ($type = $this->getRequest()->getParam('cost_type')) {
            return $this->getCostTypeDisplayName($type);
        }

        return '';
    }

    /**
     *
     * @param \SuttonBaker\Impresario\Model\Db\Cost $modelInstance
     * @param array $data
     * @return $array
     */
    public function saveCost(
        \SuttonBaker\Impresario\Model\Db\Cost $modelInstance,
        $data
    ) {

        $returnValues = [
            'cost_id' => null,
            'new_save' => false
        ];

        foreach (CostDefinition::NON_USER_VALUES as $nonUserValue) {
            if (isset($data[$nonUserValue])) {
                unset($data[$nonUserValue]);
            }
        }

        // Add created by user & set the parent for new saves
        if (!$modelInstance->getId()) {
            $data['created_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
            $returnValues['new_save'] = true;

            // Set the parent id for new items
            $costType = isset($data['cost_type']) ? $data['cost_type'] : '';
            $parentId = isset($data['parent_id']) ? $data['parent_id'] : '';

            $parent = $this->getParentItem($costType, $parentId);

            if (!$parent) {
                throw new \Exception("Parent not found");
            }
        } else {
            // Don't allow changing the parent / cost type if we're updating the record 
            unset($data['parent_id']);
            unset($data['cost_type']);
        }

        $data['last_edited_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
        
        $modelInstance->setData($data)->save();
        if (isset($data['po_items'])) {
            $this->saveCostItems($modelInstance, $data['po_items']);
        }

        if ($returnValues['new_save'] && ($temporaryId = $data[\DaveBaker\Core\Definitions\Upload::TEMPORARY_IDENTIFIER_ELEMENT_NAME])) {
            // Assign any uploads to the enquiry
            $this->getUploadHelper()->assignTemporaryUploadsToParent(
                $temporaryId,
                \SuttonBaker\Impresario\Definition\Upload::TYPE_COST,
                $modelInstance->getId()
            );
        }

        return $returnValues;
    }

    /**
     * @param string $costType
     * @param int $parentId
     * @return \SuttonBaker\Impresario\Model\Db\Project
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getParentItem(
        $costType,
        $parentId
    ) {
        $parentId = null;

        if ($costType !== CostDefinition::COST_TYPE_PROJECT) {
            throw new \Exception("Cost type $costType not supported");
        }

        return $this->getProjectHelper()->getProject($parentId);
    }


    /**
     * @param int $id
     * @param \SuttonBaker\Impresario\Model\Db\Cost\Item\Collection $costCollection
     * @return \SuttonBaker\Impresario\Model\Db\Cost\Item|null
     */
    protected function getItemWithIdFromCollection(
        $id,
        \SuttonBaker\Impresario\Model\Db\Cost\Item\Collection $costCollection
    ) {
        foreach ($costCollection->getItems() as $costItem) {
            if ((int) $costItem->getId() == (int) $id) {
                return $costItem;
            }
        }

        return null;
    }



    protected function saveCostItems(
        \SuttonBaker\Impresario\Model\Db\Cost $modelInstance,
        $items
    ) {

        if (!$modelInstance->getId()) {
            throw new Exception("Model instance must be saved before saving items");
        }

        $existingItems = $this->getCostInvoiceItems(
            $modelInstance->getId()
        );

        foreach ($items as $item) {
            // Update the item
            $itemId = (int) $item['id'];
            $isDeleted = (int) $item['removed'];

            // Don't make a new item which has been removed 
            if (!$itemId && $isDeleted) {
                continue;
            }

            if ($itemId) {
                $itemEntity = $this->getItemWithIdFromCollection(
                    $item['id'],
                    $existingItems
                );

                if (!$itemEntity) {
                    throw new \Exception("Saved item {$item['id']} not found for cost");
                };

                // If the item has previously been delted (by another user in another session) don't bother updating here
                if ((bool) $itemEntity->getIsDeleted()) {
                    continue;
                }
            } else {
                // Create a new one
                $itemEntity = $this->createAppObject(Item::class);
                $itemEntity->setCostId($modelInstance->getId());
            }

            if ($isDeleted) {
                $itemEntity->setIsDeleted(1);
            } else {
                $itemEntity->setDescription($item['description'])
                    ->setUnitPrice($item['unit_price'])
                    ->setQty($item['qty'])
                    ->setIsDeleted(0);
            }

            $itemEntity->save();
        }
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Cost $cost
     * @return $this
     */
    public function deleteCost(
        \SuttonBaker\Impresario\Model\Db\Cost $cost
    ) {
        if (!$cost->getId()) {
            return $this;
        }

        $cost->setIsDeleted(1)->save();
        return $this;
    }
}
