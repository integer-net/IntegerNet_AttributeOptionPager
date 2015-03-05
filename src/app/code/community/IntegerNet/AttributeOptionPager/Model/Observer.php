<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_AttributeOptionPager_Model_Observer
 * @copyright  Copyright (c) 2015 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */

/**
 * Module observer
 */
class IntegerNet_AttributeOptionPager_Model_Observer
{

    /**
     * @var IntegerNet_AttributeOptionPager_Model_Pager
     */
    protected $_pager;
    /**
     * @var IntegerNet_AttributeOptionPager_Model_Toolbar
     */
    protected $_toolbar;
    /**
     * @var IntegerNet_AttributeOptionPager_Helper_Data
     */
    protected $_helper;

    public function __construct()
    {
        $this->_pager = Mage::getModel('integernet_attributeoptionpager/pager');
        $this->_toolbar = Mage::getModel('integernet_attributeoptionpager/toolbar');
        $this->_helper = Mage::helper('integernet_attributeoptionpager');
    }
    /**
     * Read pagination settings from query parameters
     *
     * @event controller_action_predispatch_adminhtml_catalog_product_attribute_edit
     * @area adminhtml
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function fetchPaginationParams(Varien_Event_Observer $observer)
    {
        $this->_pager->setPageFromRequest($observer->getControllerAction()->getRequest());
        return $this;
    }
    /**
     * Sets LIMIT for attribute option collection based on pagination settings
     *
     * @event core_collection_abstract_load_before
     * @area adminhtml
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function setPageOnCollection(Varien_Event_Observer $observer)
    {
        $collection = $observer->getCollection();
        if ($collection instanceof Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection
            && !$this->_helper->isOptionCollectionStoreFiltered($collection)
        ) {
            $this->_pager->prepareCollection($collection);
            $this->_toolbar->prepareBlock($collection);
        }
        return $this;
    }

    /**
     * Revert item order in loaded collection if flag is set. This is used to maintain the
     * descending order WITHIN the page, that Magento needs.
     *
     * @event core_collection_abstract_load_after
     * @area adminhtml
     * @param Varien_Event_Observer $observer
     * @throws Exception
     */
    public function revertLoadedCollection(Varien_Event_Observer $observer)
    {
        /** @var Mage_Core_Model_Resource_Db_Collection_Abstract $collection */
        $collection = $observer->getCollection();
        if ($collection->getFlag(IntegerNet_AttributeOptionPager_Model_Pager::FLAG_REVERT_COLLECTION)) {
            $items = $collection->getItems();
            foreach ($collection as $key => $item) {
                $collection->removeItemByKey($key);
            }
            foreach (array_reverse($items) as $item) {
                $collection->addItem($item);
            }
        }
    }

    /**
     * Adds pagination toolbar to attribute option tab
     *
     * @event core_block_abstract_to_html_after
     * @area adminhtml
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addPaginationToBlock(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Options) {
            $html = $observer->getTransport()->getHtml();
            //TODO inject toolbar at top and bottom of #matage-options-panel > .box with DOM
            $paginationHtml = $this->_toolbar->getBlock()->toHtml();
            $html = $paginationHtml . '<br/>' . $html . $paginationHtml;
            $observer->getTransport()->setHtml($html);
        }

        return $this;
    }

}