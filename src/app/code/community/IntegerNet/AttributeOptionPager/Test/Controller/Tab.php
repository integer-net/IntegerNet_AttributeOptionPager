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
 * Class IntegerNet_AttributeOptionPager_Test_Controller_Tab
 *
 * @group IntegerNet_AttributeOptionPager
 */
class IntegerNet_AttributeOptionPager_Test_Controller_Tab
    extends EcomDev_PhpUnit_Test_Case_Controller
{
    /**
     * @var int Attribute ID of Magento default attribute "color"
     */
    const COLOR_ATTRIBUTE_ID = 92;

    /**
     * @test
     * @registry entity_attribute
     * @singleton integernet_attributeoptionpager/observer
     * @singleton adminhtml/session
     */
	public function paginationInLayout()
	{
	    $this->adminSession();
	    $this->dispatch('adminhtml_catalog_product_attribute_edit',
	        array('attribute_id' => self::COLOR_ATTRIBUTE_ID, '_query' => array('active_tab' => 'labels')));
	    $this->assertLayoutBlockRendered('integernet_attributeoptionpager.toolbar',
	        'Pagination toolbar should be rendered.');
	}
}