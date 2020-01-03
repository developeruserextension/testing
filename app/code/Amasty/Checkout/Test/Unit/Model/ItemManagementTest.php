<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Checkout
 */


namespace Amasty\Checkout\Test\Unit\Model;

use Amasty\Checkout\Model\ItemManagement;
use Amasty\Checkout\Test\Unit\Traits;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ItemManagementTest
 *
 * @see ItemManagement
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @codingStandardsIgnoreFile
 */
class ItemManagementTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var ItemManagement
     */
    private $model;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var \Magento\Quote\Api\Data\AddressInterface
     */
    private $address;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $cart;

    protected function setUp()
    {
        $this->address = $this->createMock(\Magento\Quote\Api\Data\AddressInterface::class);
        $this->cartRepository = $this->createMock(\Magento\Quote\Api\CartRepositoryInterface::class);
        $this->cart = $this->createMock(\Magento\Checkout\Model\Cart::class);
        $this->model = $this->getObjectManager()->getObject(
            ItemManagement::class,
            [
                'cartRepository' => $this->cartRepository,
                'cart' => $this->cart,
            ]
        );
    }

    /**
     * @covers ItemManagement::remove
     */
    public function testRemove()
    {
        $quote = $this->createMock(\Magento\Quote\Model\Quote::class);
        $quoteItem = $this->createMock(\Magento\Quote\Model\Quote\Item::class);

        $this->cartRepository->expects($this->any())->method('get')->willReturn($quote);
        $this->cartRepository->expects($this->once())->method('save');
        $quote->expects($this->any())->method('isVirtual')->will($this->onConsecutiveCalls(true, true , false, true));
        $quote->expects($this->any())->method('getItemById')->willReturn($quoteItem);
        $quoteItem->expects($this->any())->method('getId')->will($this->onConsecutiveCalls(1, 0));

        $this->model->remove(1, 2, $this->address);
        $this->assertFalse($this->model->remove(1, 2, $this->address));
    }

    /**
     * @covers ItemManagement::update
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testUpdateWithoutItem()
    {
        $quote = $this->createMock(\Magento\Quote\Model\Quote::class);

        $this->cartRepository->expects($this->any())->method('get')->willReturn($quote);
        $this->cart->expects($this->any())->method('getQuote')->willReturn($quote);
        $quote->expects($this->any())->method('isVirtual')->will($this->onConsecutiveCalls(true, true , false, true));
        $quote->expects($this->any())->method('getItemById')->willReturn(false);

        $this->model->update(1, 2, '', $this->address);
    }

    /**
     * @covers ItemManagement::update
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testUpdateWithStringItem()
    {
        $quote = $this->createMock(\Magento\Quote\Model\Quote::class);

        $this->cartRepository->expects($this->any())->method('get')->willReturn($quote);
        $this->cart->expects($this->any())->method('getQuote')->willReturn($quote);
        $this->cart->expects($this->any())->method('updateItem')->willReturn('test');
        $quote->expects($this->any())->method('isVirtual')->will($this->onConsecutiveCalls(true, true , false, true));
        $quote->expects($this->any())->method('getItemById')->willReturn($quote);

        $this->model->update(1, 2, '', $this->address);
    }

    /**
     * @covers ItemManagement::update
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testUpdateWithErrorInItem()
    {
        $quote = $this->createpartialMock(
            \Magento\Quote\Model\Quote::class,
            ['isVirtual', 'getItemById', 'getHasError']
            );

        $this->cartRepository->expects($this->any())->method('get')->willReturn($quote);
        $this->cart->expects($this->any())->method('getQuote')->willReturn($quote);
        $this->cart->expects($this->any())->method('updateItem')->willReturn($quote);
        $quote->expects($this->any())->method('isVirtual')->will($this->onConsecutiveCalls(true, true , false, true));
        $quote->expects($this->any())->method('getItemById')->willReturn($quote);
        $quote->expects($this->any())->method('getHasError')->willReturn(true);

        $this->model->update(1, 2, '', $this->address);
    }

    /**
     * @covers ItemManagement::update
     */
    public function testUpdate()
    {
        $quote = $this->createpartialMock(
            \Magento\Quote\Model\Quote::class,
            ['isVirtual', 'getItemById', 'getHasError', 'getAllVisibleItems']
        );

        $this->cartRepository->expects($this->any())->method('get')->willReturn($quote);
        $this->cart->expects($this->any())->method('getQuote')->willReturn($quote);
        $this->cart->expects($this->any())->method('updateItem')->willReturn($quote);
        $this->cart->expects($this->exactly(2))->method('save');
        $quote->expects($this->any())->method('isVirtual')->will($this->onConsecutiveCalls(false, true));
        $quote->expects($this->any())->method('getItemById')->willReturn($quote);
        $quote->expects($this->any())->method('getHasError')->willReturn(false);
        $quote->expects($this->any())->method('getAllVisibleItems')->willReturn([]);

        $this->assertFalse($this->model->update(1, 2, '', $this->address));
        $this->model->update(1, 2, '', $this->address);
    }

    /**
     * @covers ItemManagement::parseStr
     */
    public function testParseStr()
    {
        $this->assertEquals(['test1' => '2', 'test2' => '3'], $this->model->parseStr('test1=2&test2=3'));
    }

    /**
     * @covers ItemManagement::prepareParams
     */
    public function testPrepareParams()
    {
        $this->assertEquals(
            ['id' => 1, 'options' => []],
            $this->invokeMethod($this->model, 'prepareParams', [['options' => []], 1])
        );

        $this->assertEquals(
            ['id' => 1, 'options' => []],
            $this->invokeMethod($this->model, 'prepareParams', [[], 1])
        );

        $this->assertEquals(
            ['id' => 1, 'options' => [], 'qty' => 2, 'reset_count' => true],
            $this->invokeMethod($this->model, 'prepareParams', [['qty' => 2], 1])
        );
    }
}