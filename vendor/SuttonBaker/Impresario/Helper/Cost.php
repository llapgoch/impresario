<?php

namespace SuttonBaker\Impresario\Helper;

use DaveBaker\Core\Definitions\Upload;
use Exception;
use \SuttonBaker\Impresario\Definition\Cost as CostDefinition;
use SuttonBaker\Impresario\Definition\Invoice as DefinitionInvoice;
use SuttonBaker\Impresario\Definition\Roles;
use SuttonBaker\Impresario\Model\Db\Cost\Item;
use SuttonBaker\Impresario\Model\Db\Invoice;
use SuttonBaker\Impresario\Model\Db\Project;

/**
 * Class Cost
 * @package SuttonBaker\Impresario\Helper
 */
class Cost extends Base
{
    /** @var string */
    const ACTION_DIRECT_TO_INVOICE = 'direct_to_invoice';
    /** @var array  */
    protected $editCapabilities = [Roles::CAP_ALL, Roles::CAP_EDIT_COST];
    /** @var array  */
    protected $viewCapabilities = [Roles::CAP_ALL, Roles::CAP_EDIT_COST, Roles::CAP_VIEW_COST];

    protected $costInvoiceCache = [];
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
    public function getCostInvoiceItems($costInvoiceId, $reload = false)
    {
        if (!isset($this->costInvoiceCache[$costInvoiceId]) || $reload) {
            /** @var \SuttonBaker\Impresario\Model\Db\Cost\Item\Collection $collection */
            $collection = $this->createAppObject(
                \SuttonBaker\Impresario\Model\Db\Cost\Item\Collection::class
            )->where('{{cost_po_item}}.is_deleted=?', '0')
                ->where('{{cost_po_item}}.cost_id=?', $costInvoiceId);

            $this->costInvoiceCache[$costInvoiceId] = $collection;
        }

        return $this->costInvoiceCache[$costInvoiceId];
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
     * @param $status
     * @return string
     */
    public function getStatusDisplayName($status)
    {
        return $this->getDisplayName($status, CostDefinition::getStatuses());
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
     * @return \SuttonBaker\Impresario\Helper\OutputProcessor\Cost\Status
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getCostStatusOutputProcessor()
    {
        return $this->createAppObject(\SuttonBaker\Impresario\Helper\OutputProcessor\Cost\Status::class);
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

    public function isCostLocked(
        \SuttonBaker\Impresario\Model\Db\Cost $instance
    ) {
        if (!$instance->getId()) {
            return false;
        }

        $parent = $this->getParentForCost($instance);

        if ($parent instanceof Project) {
            return $this->getProjectHelper()->isProjectLocked($parent);
        }

        return false;
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
            'new_save' => false,
            'direct_to_invoice' => false
        ];

        $directToInvoice = isset($data['action']) && $data['action'] == self::ACTION_DIRECT_TO_INVOICE;

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

        // Save first to get the ID
        $modelInstance->setData($data)->save();

        // Save each cost item, calculating their totals
        if (isset($data['po_items'])) {
            $this->saveCostItems($modelInstance, $data['po_items']);
        }

        // Re-save the model instance to calculate the total after setting the items.
        $modelInstance->save();

        // Create an invoice for the PO value automatically, only for new saves
        if ($returnValues['new_save'] && $directToInvoice) {
            $invoiceEntity = $this->createAppObject(Invoice::class)
                ->setInvoiceDate($modelInstance->getCostDate())
                ->setInvoiceNumber("AUTO-INVOICE-PO-#{$modelInstance->getId()}")
                ->setValue($modelInstance->calculatePoItemTotal())
                ->setInvoiceType(DefinitionInvoice::INVOICE_TYPE_PO_INVOICE)
                ->setParentId($modelInstance->getId());

            $invoiceEntity->save();

            // Close the PO item
            $modelInstance->setStatus(CostDefinition::STATUS_CLOSED)
                ->save();

            $returnValues['direct_to_invoice'] = true;
        }

        if ($returnValues['new_save'] && ($temporaryItems = $data[Upload::TEMPORARY_IDENTIFIER_ELEMENT_NAME])) {
            foreach ($temporaryItems as $temporaryId => $actualKey) {
                $this->getUploadHelper()->assignTemporaryUploadsToParent(
                    $temporaryId,
                    $actualKey,
                    $modelInstance->getId()
                );
            }
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
