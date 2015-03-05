<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_AttributeOptionPager_Model_Observer
 * @copyright  Copyright (c) 2015 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */

require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml') . '/Catalog/Product/AttributeController.php';

/**
 * Class IntegerNet_AttributeOptionPager_Test_Block_Tab
 *
 * @group IntegerNet_AttributeOptionPager
 */
class IntegerNet_AttributeOptionPager_Test_Block_Tab extends EcomDev_PHPUnit_Test_Case
{
    const TEST_ATTRIBUTE_CODE  = '__test_attribute';

    /**
     * @var Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Options
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_removeAttribute();
        $this->_addAttribute();
        $this->_block = $this->app()->getLayout()
            ->createBlock('adminhtml/catalog_product_attribute_edit_tab_options');
        $this->app()->addEventArea('adminhtml');
    }
    protected function tearDown()
    {
        $this->_removeAttribute();
        parent::tearDown();
    }

    /**
     * Fixture: Create test attribute
     *
     * @return $this
     */
    protected function _addAttribute()
    {
        /** @var Mage_Catalog_Model_Resource_Setup $setup */
        $setup = Mage::getResourceModel('catalog/setup', 'catalog_setup');
        $setup->startSetup();
        $setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, self::TEST_ATTRIBUTE_CODE, array(
            'type'                       => 'int',
            'label'                      => 'TEST',
            'input'                      => 'select',
            'required'                   => false,
            'user_defined'               => true,
            'searchable'                 => false,
            'filterable'                 => false,
            'comparable'                 => false,
            'visible_in_advanced_search' => false,
            'apply_to'                   => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
            'option'                     => array(
                'values' => range(101,199)
            ),
        ));
        $setup->endSetup();
        return $this;
    }

    /**
     * Fixture: Remove test attribute
     *
     * @return $this
     */
    protected function _removeAttribute()
    {
        /** @var Mage_Catalog_Model_Resource_Setup $setup */
        $setup = Mage::getResourceModel('catalog/setup', 'catalog_setup');
        $setup->removeAttribute(Mage_Catalog_Model_Product::ENTITY, self::TEST_ATTRIBUTE_CODE);
        return $this;
    }

    /**
     * Test loading of collection with pagination request
     *
     * @test
     * @dataProvider dataProvider
     * @loadExpectation
     * @registry entity_attribute
     * @singleton integernet_attributeoptionpager/observer
     * @param int $requestPage
     * @param $requestPageSize
     * @param $configPageSize
     * @throws Mage_Core_Exception
     */
    public function loadCollection($requestPage, $requestPageSize, $configPageSize)
    {
        $this->_mockToolbar();
        $this->app()->getStore('admin')->setConfig(
            IntegerNet_AttributeOptionPager_Model_Pager::XML_PAGE_SIZE, $configPageSize);

        $this->_registerEntityAttribute();
        $this->_triggerPredispatchObserver($requestPage, $requestPageSize);

        $actualOptionValues = array_map(
            function($value) { return $value['store0']; },
            $this->_block->getOptionValues());
        $expectedOptionValues = $this->expected('page-%s-size-%d', $requestPage, $requestPageSize ?: $configPageSize)
            ->getOptions();
        $this->assertEquals($expectedOptionValues, $actualOptionValues);
    }

    /**
     * To emulate the request we trigger the predispatch observer with our page parameter manually
     *
     * @param $requestPage
     * @return $this
     */
    protected function _triggerPredispatchObserver($requestPage, $requestPageSize)
    {
        $request = new Mage_Core_Controller_Request_Http();
        $response = new Mage_Core_Controller_Response_Http();
        $request->setParam('page', $requestPage);
        $request->setParam('limit', $requestPageSize);
        $controller = new Mage_Adminhtml_Catalog_Product_AttributeController($request, $response);
        $observer = $this->generateObserver(array('controller_action' => $controller),
            'controller_action_predispatch_adminhtml_catalog_product_attribute_edit');
        Mage::getSingleton('integernet_attributeoptionpager/observer')->fetchPaginationParams($observer);
        return $this;
    }

    /**
     * Mock toolbar (layout is not loaded)
     */
    protected function _mockToolbar()
    {
        $toolbarMock = $this->mockModel('integernet_attributeoptionpager/toolbar', array('prepareBlock'));
        $toolbarMock->expects($this->once())
            ->method('prepareBlock')->with(
                $this->isInstanceOf('Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection'))
            ->will($this->returnSelf());
        $this->replaceByMock('model', 'integernet_attributeoptionpager/toolbar', $toolbarMock);
    }

    /**
     * Registers test attribute for tab block (would be done in controller action otherwise)
     *
     * @throws Mage_Core_Exception
     */
    protected function _registerEntityAttribute()
    {
        $attribute = Mage::getModel('eav/entity_attribute')
            ->loadByCode(Mage_Catalog_Model_Product::ENTITY, self::TEST_ATTRIBUTE_CODE);
        Mage::register('entity_attribute', $attribute);
    }
}