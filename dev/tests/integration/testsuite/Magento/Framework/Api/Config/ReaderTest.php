<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Api\Config;

/**
 * Tests for \Magento\Framework\Api\Config\Reader
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Api\Config\Reader
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_fileList;

    /**
     * @var \Magento\Framework\App\Arguments\FileResolver\Primary
     */
    protected $_fileResolverMock;

    /**
     * @var \Magento\Framework\App\Arguments\ValidationState
     */
    protected $_validationState;

    /**
     * @var \Magento\Framework\Api\Config\SchemaLocator
     */
    protected $_schemaLocator;

    /**
     * @var \Magento\Framework\Api\Config\Converter
     */
    protected $_converter;

    protected function setUp()
    {
        $fixturePath = realpath(__DIR__ . '/_files') . '/';
        $this->_fileList = [
            file_get_contents($fixturePath . 'config_one.xml'),
            file_get_contents($fixturePath . 'config_two.xml'),
        ];

        $this->_fileResolverMock = $this->getMockBuilder('Magento\Framework\App\Arguments\FileResolver\Primary')
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();
        $this->_fileResolverMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($this->_fileList));

        $this->_converter = new \Magento\Framework\Api\Config\Converter();

        $this->_validationState = new \Magento\Framework\App\Arguments\ValidationState(
            \Magento\Framework\App\State::MODE_DEFAULT
        );
        $this->_schemaLocator = new \Magento\Framework\Api\Config\SchemaLocator();
    }

    public function testMerge()
    {
        $model = new \Magento\Framework\Api\Config\Reader(
            $this->_fileResolverMock,
            $this->_converter,
            $this->_schemaLocator,
            $this->_validationState
        );

        $expectedArray = [
            'Magento\Tax\Api\Data\TaxRateInterface' => [],
            'Magento\Catalog\Api\Data\Product' => [
                'stock_item' => [
                    "type" => "Magento\CatalogInventory\Api\Data\StockItem",
                    "resourceRefs" => [],
                ],
            ],
            'Magento\Customer\Api\Data\CustomerInterface' => [
                'custom_1' => [
                    "type" => "Magento\Customer\Api\Data\CustomerCustom",
                    "resourceRefs" => [],
                ],
                'custom_2' => [
                    "type" => "Magento\CustomerExtra\Api\Data\CustomerCustom22",
                    "resourceRefs" => [],
                ],
                'custom_3' => [
                    "type" => "Magento\Customer\Api\Data\CustomerCustom3",
                    "resourceRefs" => [],
                ],
            ],
        ];

        $this->assertEquals($expectedArray, $model->read('global'));
    }
}
