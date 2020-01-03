<?php

namespace Feefo\Reviews\Test\Unit\Block;

use Feefo\Reviews\Api\Feefo\Data\WidgetConfigInterface;
use Feefo\Reviews\Api\Feefo\Data\WidgetSnippetInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Feefo\Reviews\Block\ProductListing;
use Feefo\Reviews\Model\Feefo\Storage;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Feefo\Reviews\Test\Unit\ObjectManagerFactory;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\View\Element\Template\File\Resolver as FileResolver;
use Magento\Framework\View\Element\Template\File\Validator as TemplateFileValidator;
use Magento\Framework\View\TemplateEnginePool;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface as WebsiteDataInterface;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ProductListingTest
 */
class ProductListingTest extends AbstractTestCase
{
    /**
     * @var ProductListing
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
        $this->appState->setAreaCode(Area::AREA_FRONTEND);

        $arguments = [
            'data' => [],
        ];
        $context = $this->createTemplateContext();
        $arguments['context'] = $context;

        $this->widgetSnippets = $this->createWidgetSnippetsMock();
        $this->widgetSettings = $this->createWidgetSettingsMock();
        $this->storage = $this->createWidgetSnippetStorage($this->widgetSnippets, $this->widgetSettings);
        $arguments['storage'] = $this->storage;

        $this->storeManager = $context->getStoreManager();
        $this->storeMock = $this->basicMock(StoreInterface::class);
        $this->configureCurrentStoreId(1);
        $this->block = $this->objectManager->getObject(ProductListing::class, $arguments);
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

        $this->configureProductListingPlacement($blockPlacement);
        $this->configureProductListingStatus($isWidgetEnabled);

        $this->block->setData('placement', $configuredPlacement);
        $html = $this->block->toHtml();

        self::assertNotContains('feefo-product-list-rating-container', $html);
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

        $this->configureProductListingPlacement($configuredPlacement);
        $this->configureProductListingStatus($isWidgetEnabled);

        $this->block->setData('placement', $blockPlacement);
        $html = $this->block->toHtml();

        self::assertContains('feefo-product-list-rating-container', $html);
    }

    /**
     * @dataProvider snippetProvider
     * @param string $snippet
     * @param string $productId
     * @return void
     */
    public function testWidgetSnippet($snippet, $productId, $expected)
    {
        $productMock = $this->createProductMock($productId);

        $this->configureWebsiteId('13');
        $websiteDataMock = $this->createWebsiteDataModelMock('13');
        $this->configureCurrentWebsite($websiteDataMock);

        $this->configureProductListingPlacement(WidgetConfigInterface::PLACEMENT_AUTO);
        $this->configureProductListingStatus(true);
        $this->configureWidgetSnippet($snippet);

        $this->block->setData('placement', WidgetConfigInterface::PLACEMENT_AUTO);
        $this->block->setCurrentProduct($productMock);
        $html = $this->block->toHtml();

        self::assertContains($expected, $html);
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
            ['//register-uat.feefo.com/api/logo?merchantidentifier=site-info&parentvendorref={{ product.id }}&template={{ template.name }}', '1', '//register-uat.feefo.com/api/logo?merchantidentifier=site-info&parentvendorref=1&template=product-page-orange-stars-only-85x18.png'],
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
            ->setMethods(['getProductListSnippet'])
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
            ->setMethods(['getProductListingStarsPlacement', 'isProductListingStars'])
            ->getMockForAbstractClass();

        return $widgetSettings;
    }

    /**
     * @param string $configuredPlacement
     * @return void
     */
    protected function configureProductListingPlacement($configuredPlacement)
    {
        $this->widgetSettings->expects($this->any())
            ->method('getProductListingStarsPlacement')
            ->willReturn($configuredPlacement);
    }

    /**
     * @param boolean $isWidgetEnabled
     * @return void
     */
    protected function configureProductListingStatus($isWidgetEnabled)
    {
        $this->widgetSettings->expects($this->any())
            ->method('isProductListingStars')
            ->willReturn($isWidgetEnabled);
    }

    /**
     * @param string $snippet
     * @return void
     */
    protected function configureWidgetSnippet($snippet)
    {
        $this->widgetSnippets->expects($this->any())
            ->method('getProductListSnippet')
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
}