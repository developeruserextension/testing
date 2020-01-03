<?php
/**
 * @author Atwix Team
 * @copyright Copyright (c) 2018 Atwix (https://www.atwix.com/)
 * @package Feefo_Reviews
 */
namespace Feefo\Reviews\Test\Unit\Plugin\Block\Product;

use Closure;
use Feefo\Reviews\Plugin\Block\Product\ReviewRendererPlugin;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Review\Block\Product\ReviewRenderer;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class ReviewRendererPluginTest
 */
class ReviewRendererPluginTest extends AbstractTestCase
{
    /**
     * Testable object
     *
     * @var ReviewRendererPlugin
     */
    protected $sut;

    /**
     * Subject ReviewRenderer
     *
     * @var ReviewRenderer|Mock
     */
    protected $subjectMock;

    /**
     * Closure
     *
     * @var Closure|Mock
     */
    protected $proceedMock;

    /**
     * Product
     *
     * @var Product|Mock
     */
    protected $productMock;

    /**
     * Layout
     *
     * @var LayoutInterface|Mock
     */
    protected $layoutMock;

    /**
     * Block
     *
     * @var Template|Mock
     */
    protected $blockMock;

    /**
     * Test set up
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->subjectMock = $this->basicMock(ReviewRenderer::class);
        $this->proceedMock = $deleteCustomerById = function () {
            return true;
        };
        $this->productMock = $this->basicMock(Product::class);
        $this->layoutMock = $this->basicMock(LayoutInterface::class);
        $this->blockMock = $this->mixedMock(Template::class, [
            'setCurrentProduct',
            'isEnabledWidget',
            'isRightPlacement',
            'toHtml'
        ]);

        $this->sut = new ReviewRendererPlugin();
    }

    /**
     * Test aroundGetReviewsSummaryHtml method
     *
     * @test
     *
     * @return void
     *
     * @throws LocalizedException
     */
    public function testAroundGetReviewsSummaryHtml() {

        $expected = '<div class="feefo">Rating</div>';

        $this->subjectMock->expects($this->once())
            ->method('getLayout')
            ->willReturn($this->layoutMock);

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->willReturn($this->blockMock);

        $this->blockMock->expects($this->once())
            ->method('setCurrentProduct')
            ->willReturnSelf();

        $this->blockMock->expects($this->once())
            ->method('isEnabledWidget')
            ->willReturn('true');

        $this->blockMock->expects($this->once())
            ->method('isRightPlacement')
            ->willReturn('true');

        $this->blockMock->expects($this->once())
            ->method('toHtml')
            ->willReturn($expected);

        $actual = $this->sut->aroundGetReviewsSummaryHtml(
            $this->subjectMock,
            $this->proceedMock,
            $this->productMock
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test aroundGetReviewsSummaryHtml method
     *
     * @test
     *
     * @return void
     *
     * @throws LocalizedException
     */
    public function testAroundGetReviewsSummaryHtmlOrigin() {

        $expected = '<div class="product-reviews-summary">Rating</div>';

        $this->subjectMock->expects($this->once())
            ->method('getLayout')
            ->willReturn($this->layoutMock);

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->willReturn($this->blockMock);

        $this->blockMock->expects($this->once())
            ->method('setCurrentProduct')
            ->willReturnSelf();

        $product = $this->proceedMock;
        $proceed = function ($product) use ($expected) {
            return $expected;
        };

        $actual = $this->sut->aroundGetReviewsSummaryHtml(
            $this->subjectMock,
            $proceed,
            $product
        );

        $this->assertEquals($expected, $actual);
    }
}