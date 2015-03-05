<?php

/**
 * Provider for toolbar block
 */
class IntegerNet_AttributeOptionPager_Model_Toolbar
{
    const BLOCK_TOOLBAR = 'integernet_attributeoptionpager.toolbar';

    /**
     * @var Mage_Page_Block_Html_Pager
     */
    protected $_block;

    /**
     * @param $collection
     * @return $this
     */
    public function prepareBlock($collection)
    {
        $this->getBlock()
            ->setCollection($collection)
            ->setAvailableLimit(array('20' => '20', '50' => '50', '100' => '100', '250' => '250', '1000' => '1000'));
        return $this;
    }

    /**
     * @return Mage_Page_Block_Html_Pager
     */
    public function getBlock()
    {
        if (is_null($this->_block)) {
            $this->_block = Mage::app()->getLayout()->getBlock(self::BLOCK_TOOLBAR);
        }
        return $this->_block;
    }
}