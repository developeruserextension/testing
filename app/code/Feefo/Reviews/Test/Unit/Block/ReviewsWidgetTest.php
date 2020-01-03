<?php

namespace Feefo\Reviews\Test\Unit\Block;

use Feefo\Reviews\Api\Feefo\Data\WidgetConfigInterface;
use Feefo\Reviews\Api\Feefo\Data\WidgetSnippetInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Feefo\Reviews\Block\ReviewsWidget;
use Feefo\Reviews\Model\Feefo\Storage;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Feefo\Reviews\Test\Unit\ObjectManagerFactory;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\View\Element\Template\File\Resolver as FileResolver;
use Magento\Framework\View\Element\Template\File\Validator as TemplateFileValidator;
use Magento\Framework\View\TemplateEnginePool;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface as WebsiteDataInterface;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ReviewsWidgetTest
 */
class ReviewsWidgetTest extends AbstractTestCase
{
    /**
     * @var ReviewsWidget
     */
    protected $block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StorageInterface
     */
    protected $storage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ObjectManagerInterface
     */
    protected $realObjectManager;

    /**
     * @var AppState
     */
    protected $appState;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|WidgetSnippetInterface
     */
    protected $widgetSnippets;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|WidgetConfigInterface
     */
    protected $widgetSettings;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StoreInterface
     */
    protected $storeMock;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->initRealObjectManager();

        $this->appState = $this->realObjectManager->get(AppState::class);
        $this->appState->setAreaCode('frontend');

        $arguments = [
            'data' => [],
        ];
        $context = $this->createTemplateContext();
        $arguments['context'] = $context;

        $this->widgetSnippets = $this->createWidgetSnippetsMock();
        $this->widgetSettings = $this->createWidgetSettingsMock();

        $this->storage = $this->createWidgetSnippetStorage($this->widgetSnippets, $this->widgetSettings);
        $arguments['storage'] = $this->storage;

        $this->registry = $this->createRegistry();
        $arguments['registry'] = $this->registry;

        $this->storeManager = $context->getStoreManager();
        $this->storeMock = $this->basicMock(StoreInterface::class);
        $this->configureCurrentStoreId(1);
        $this->block = $this->objectManager->getObject(ReviewsWidget::class, $arguments);
    }

    /**
     * @dataProvider disableStatusProvider
     * @param string $configuredWebsiteId
     * @param string $currentWebsiteId
     * @param boolean $isWidgetEnabled
     * @param string $blockPlacement
     * @param string $configuredPlacement
     * @return void
     */
    public function testWidgetDisabled($configuredWebsiteId, $currentWebsiteId, $isWidgetEnabled, $blockPlacement, $configuredPlacement)
    {
        $this->configureWebsiteId($configuredWebsiteId);
        $websiteDataMock = $this->createWebsiteDataModelMock($currentWebsiteId);
        $this->configureCurrentWebsite($websiteDataMock);

        $this->configureProductWidgetPlacement($blockPlacement);
        $this->configureProductReviewsStatus($isWidgetEnabled);

        $this->block->setData('placement', $configuredPlacement);
        $html = $this->block->toHtml();

        self::assertNotContains('feefo-review-container', $html);
    }

    /**
     * @dataProvider enableStatusProvider
     * @param string $configuredWebsiteId
     * @param string $currentWebsiteId
     * @param $isWidgetEnabled
     * @param string $blockPlacement
     * @param string $configuredPlacement
     * @return void
     */
    public function testWidgetEnabled($configuredWebsiteId, $currentWebsiteId, $isWidgetEnabled, $blockPlacement, $configuredPlacement)
    {
        $this->configureWebsiteId($configuredWebsiteId);
        $websiteDataMock = $this->createWebsiteDataModelMock($currentWebsiteId);
        $this->configureCurrentWebsite($websiteDataMock);

        $this->configureProductWidgetPlacement($configuredPlacement);
        $this->configureProductReviewsStatus($isWidgetEnabled);

        $this->block->setData('placement', $blockPlacement);
        $html = $this->block->toHtml();

        self::assertContains('feefo-review-container', $html);
    }

    /**
     * @dataProvider snippetProvider
     * @param string $snippet
     * @param string $productId
     * @param string $expected
     * @return void
     */
    public function testWidgetSnippet($snippet, $productId, $expected)
    {
        $productMock = $this->createProductMock($productId);
        $this->registerProduct($productMock);

        $this->configureWebsiteId('13');
        $websiteDataMock = $this->createWebsiteDataModelMock('13');
        $this->configureCurrentWebsite($websiteDataMock);

        $this->configureProductWidgetPlacement(WidgetConfigInterface::PLACEMENT_AUTO);
        $this->configureProductReviewsStatus(true);
        $this->configureWidgetSnippet($snippet);

        $this->block->setData('placement', WidgetConfigInterface::PLACEMENT_AUTO);
        $html = $this->block->toHtml();

        self::assertContains($expected, $html);
    }

    /**
     * @return void
     */
    public function testWidgetRenderTwice()
    {
        $productMock = $this->createProductMock('13');
        $this->registerProduct($productMock);

        $this->configureWebsiteId('13');
        $websiteDataMock = $this->createWebsiteDataModelMock('13');
        $this->configureCurrentWebsite($websiteDataMock);

        $this->configureProductWidgetPlacement(WidgetConfigInterface::PLACEMENT_AUTO);
        $this->configureProductReviewsStatus(true);
        $this->configureWidgetSnippet('sample');

        $this->block->setData('placement', WidgetConfigInterface::PLACEMENT_AUTO);

        $html = $this->block->toHtml();
        self::assertContains('feefo-review-container', $html);

        $html = $this->block->toHtml();
        self::assertNotContains('feefo-review-container', $html);
    }

    /**
     * @return array
     */
    public function disableStatusProvider()
    {
        return [
            ['2', '2000', false, WidgetConfigInterface::PLACEMENT_AUTO, WidgetConfigInterface::PLACEMENT_AUTO],
            ['99', '99', false, WidgetConfigInterface::PLACEMENT_AUTO, WidgetConfigInterface::PLACEMENT_AUTO],
            ['99', '99', true, WidgetConfigInterface::PLACEMENT_CUSTOM, WidgetConfigInterface::PLACEMENT_AUTO],
            ['99', '99', true, WidgetConfigInterface::PLACEMENT_AUTO, WidgetConfigInterface::PLACEMENT_CUSTOM],
        ];
    }

    /**
     * @return array
     */
    public function enableStatusProvider()
    {
        return [
            ['13', '13', true, WidgetConfigInterface::PLACEMENT_AUTO, WidgetConfigInterface::PLACEMENT_AUTO],
            ['13', '13', true, WidgetConfigInterface::PLACEMENT_CUSTOM, WidgetConfigInterface::PLACEMENT_CUSTOM],
        ];
    }

    /**
     * @return array
     */
    public function snippetProvider()
    {
        return [
            ['<div id=\"feefo-product-review-widgetId\" class=\"feefo-review-widget-product\" data-feefo-product-id=\"{{ product.id }}\"></div>\n', '1', '<div id=\"feefo-product-review-widgetId\" class=\"feefo-review-widget-product\" data-feefo-product-id=\"1\"></div>\n'],
        ];
    }

    /**
     * @param string $id
     * @return \PHPUnit_Framework_MockObject_MockObject|WebsiteDataInterface
     */
    protected function createWebsiteDataModelMock($id = '1')
    {
        $websiteDataMock = $this->getMockBuilder(WebsiteDataInterface::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $websiteDataMock->expects($this->any())
            ->method('getId')
            ->willReturn($id);

        return $websiteDataMock;
    }

    /**
     * @param string $id
     * @return void
     */
    protected function configureWebsiteId($id)
    {
        $this->storage->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($id);
    }

    /**
     * @param $websiteDataMock
     * @return void
     */
    protected function configureCurrentWebsite($websiteDataMock)
    {
        $this->storeManager->expects($this->any())
            ->method('getWebsite')
            ->willReturn($websiteDataMock);
    }

    /**
     * @return TemplateContext
     */
    protected function createTemplateContext()
    {
        $contextArguments = $this->objectManager->getConstructArguments(TemplateContext::class);
        $contextArguments['filesystem'] = $this->createFilesystemObject();
        $contextArguments['resolver'] = $this->createFileResolver();
        $contextArguments['validator'] = $this->createTemplateFileValidator();
        $contextArguments['enginePool'] = $this->createEnginePool();
        $contextArguments['storeManager'] = $this->createStoreManagerMock();
        return $this->objectManager->getObject(TemplateContext::class, $contextArguments);
    }

    /**
     * @return Filesystem
     */
    protected function createFilesystemObject()
    {
        $filesystem = $this->realObjectManager->create(Filesystem::class);

        return $filesystem;
    }

    /**
     * @return FileResolver
     */
    protected function createFileResolver()
    {
        $fileResolver = $this->realObjectManager->create(FileResolver::class);

        return $fileResolver;
    }

    /**
     * @return TemplateFileValidator
     */
    protected function createTemplateFileValidator()
    {
        return $this->realObjectManager->create(TemplateFileValidator::class);
    }

    /**
     * @return TemplateEnginePool
     */
    protected function createEnginePool()
    {
        return $this->realObjectManager->create(TemplateEnginePool::class);
    }

    /**
     * @return void
     */
    protected function initRealObjectManager()
    {
        $realObjectManagerFactory = new ObjectManagerFactory();
        $this->realObjectManager = $realObjectManagerFactory->create();
        $frontendConfigurations = $this->realObjectManager
            ->get(ConfigLoader::class)
            ->load(Area::AREA_FRONTEND);
        $this->realObjectManager->configure($frontendConfigurations);
    }

    /**
     * @param WidgetSnippetInterface $widgetSnippets
     * @param WidgetConfigInterface $widgetConfigs
     * @return StorageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createWidgetSnippetStorage($widgetSnippets, $widgetConfigs)
    {
        $storage = $this->basicMock(Storage::class);

        $storage->expects($this->any())
            ->method('getWidgetSnippets')
            ->willReturn($widgetSnippets);

        $storage->expects($this->any())
            ->method('getWidgetSettings')
            ->willReturn($widgetConfigs);

        $storage->expects($this->any())
            ->method('getByStoreId')
            ->willReturn('test');

        return $storage;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|WidgetSnippetInterface
     */
    protected function createWidgetSnippetsMock()
    {
        $widgetSnippets = $this->getMockBuilder(WidgetSnippetInterface::class)
            ->setMethods(['getProductBaseSnippet'])
            ->getMockForAbstractClass();

        return $widgetSnippets;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|StoreManager
     */
    protected function createStoreManagerMock()
    {
        return $this->basicMock(StoreManager::class);
    }

    /**
     * @return WidgetConfigInterface
     */
    protected function createWidgetSettingsMock()
    {
        $widgetSettings = $this->getMockBuilder(WidgetConfigInterface::class)
            ->setMethods(['getProductWidgetPlacement', 'isProductReviewsWidget'])
            ->getMockForAbstractClass();

        return $widgetSettings;
    }

    /**
     * @param string $configuredPlacement
     * @return void
     */
    protected function configureProductWidgetPlacement($configuredPlacement)
    {
        $this->widgetSettings->expects($this->any())
            ->method('getProductWidgetPlacement')
            ->willReturn($configuredPlacement);
    }

    /**
     * @param boolean $isWidgetEnabled
     * @return void
     */
    protected function configureProductReviewsStatus($isWidgetEnabled)
    {
        $this->widgetSettings->expects($this->any())
            ->method('isProductReviewsWidget')
            ->willReturn($isWidgetEnabled);
    }

    /**
     * @param string $snippet
     * @return void
     */
    protected function configureWidgetSnippet($snippet)
    {
        $this->widgetSnippets->expects($this->any())
            ->method('getProductBaseSnippet')
            ->willReturn($snippet);
    }

    /**
     * @param string $productId
     * @return \PHPUnit_Framework_MockObject_MockObject|ProductModel
     */
    protected function createProductMock($productId)
    {
        $productMock = $this->getMockBuilder(ProductModel::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();

        $productMock->expects($this->any())
            ->method('getId')
            ->willReturn($productId);

        return $productMock;
    }

    /**
     * @return Registry
     */
    protected function createRegistry()
    {
        $registry = $this->realObjectManager->get(Registry::class);

        return $registry;
    }

    /**
     * @param string $productMock
     * @return void
     */
    protected function registerProduct($productMock)
    {
        $this->registry->register('product', $productMock);
    }

    /**
     * @param boolean $wasCalled
     * @return void
     */
    protected function registerIsAlreadyCalled($wasCalled)
    {
        $this->registry->register(ReviewsWidget::KEY_ALREADY_REGISTRED, $wasCalled);
    }

    /**
     * @param string $id
     * @return void
     */
    protected function configureCurrentStoreId($id)
    {
        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->willReturn($this->storeMock);
        $this->storeMock->expects($this->any())
            ->method('getId')
            ->willReturn($id);
    }
}