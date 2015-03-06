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
 * Default module helper
 */
class IntegerNet_AttributeOptionPager_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Returns true if given option collection has store filter applied to select values per store.
     *
     * If so, the pagination must not be applied
     *
     * @see IntegerNet_AttributeOptionPager_Model_Observer::setPageOnCollection()
     * @see Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection::setStoreFilter()
     *
     * @param Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection $collection
     * @return bool
     * @throws Zend_Db_Select_Exception
     */
    public function isOptionCollectionStoreFiltered(
        Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection $collection)
    {
        $joins = $collection->getSelect()->getPart(Zend_Db_Select::FROM);
        return array_key_exists('tsv', $joins) || array_key_exists('tdv', $joins);
    }
}