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
 * Sets page based on request
 */
class IntegerNet_AttributeOptionPager_Model_Pager
{
    const XML_PAGE_SIZE = 'catalog_attributes/pagination/page_size';
    const FLAG_REVERT_COLLECTION = 'integernet_revert_collection';

    protected $_currentPage = 1;
    protected $_pageSize = null;

    public function __construct()
    {
        $this->_pageSize = Mage::getStoreConfig(IntegerNet_AttributeOptionPager_Model_Pager::XML_PAGE_SIZE);
    }

    /**
     * Read settings
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return $this
     */
    public function setPageFromRequest(Mage_Core_Controller_Request_Http $request)
    {
        $this->_currentPage = max(1, $request->getParam('page'));
        $limit = $request->getParam('limit', null);
        if ($limit !== null) {
            $this->_pageSize = (int)$limit;
        }
        return $this;
    }

    /**
     * Set collection parameters
     *
     * @param Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection $collection
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection
     */
    public function prepareCollection(Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection $collection)
    {
        if ($this->_pageSize && $this->_currentPage) {
            $collection
                ->setPageSize($this->_pageSize)
                ->setCurPage($this->_currentPage)
                ->setOrder('main_table.sort_order', Zend_Db_Select::SQL_ASC);
            if (array_key_exists('sort_alpha_value', $collection->getSelect()->getPart(Zend_Db_Select::FROM))) {
                $collection->addOrder('sort_alpha_value.value', Zend_Db_Select::SQL_ASC);
            }
            $collection->setFlag(self::FLAG_REVERT_COLLECTION, true);
        }
        return $collection;
    }
}