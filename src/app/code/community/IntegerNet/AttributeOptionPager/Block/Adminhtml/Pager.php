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
 * Pagination block
 */
class IntegerNet_AttributeOptionPager_Block_Adminhtml_Pager extends Mage_Page_Block_Html_Pager
{
    protected $_pageVarName    = 'page';

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('integernet_attributeoptionpager/pager.phtml');
    }

    /**
     * Set collection without manipulating or initializing frame. This would result in inconsistencies
     * because the collection is already loaded.
     * Instead, use limit that already has been applied on collection.
     *
     * @param Varien_Data_Collection $collection
     * @return $this|Mage_Page_Block_Html_Pager
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        $this->setLimit($collection->getPageSize());
        return $this;
    }

    public function getPagerUrl($params = array())
    {
        $params['active_tab'] = 'labels';
        return parent::getPagerUrl($params);
    }


}