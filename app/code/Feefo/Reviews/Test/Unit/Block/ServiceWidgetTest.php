<?php

namespace Feefo\Reviews\Test\Unit\Block;

use Feefo\Reviews\Api\Feefo\Data\WidgetConfigInterface;
use Feefo\Reviews\Api\Feefo\Data\WidgetSnippetInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Feefo\Reviews\Block\ServiceWidget;
use Feefo\Reviews\Model\Feefo\Storage;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Feefo\Reviews\Test\Unit\ObjectManagerFactory;
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
 * Class ServiceWidgetTest
 */
class ServiceWidgetTest extends AbstractTestCase
{
    /**
     * @var ServiceWidget
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

        $arguments = $this->objectManager->getConstructArguments(ServiceWidget::class);
        $context = $this->createTemplateContext();
        $arguments['context'] = $context;

        $this->widgetSnippets = $this->createWidgetSnippets();
        $this->storage = $this->createWidgetSnippetStorage($this->widgetSnippets);
        $arguments['storage'] = $this->storage;

        $this->storeManager = $context->getStoreManager();
        $this->storeMock = $this->basicMock(StoreInterface::class);
        $this->configureCurrentStoreId(1);
        $this->block = $this->objectManager->getObject(ServiceWidget::class, $arguments);
    }


    /**
     * @dataProvider serviceWidgetProvider
     * @param string $widgetSnippet
     * @return void
     */
    public function testContent($widgetSnippet)
    {
        $this->widgetSnippets->expects($this->any())
            ->method('getServiceSnippet')
            ->willReturn($widgetSnippet);

        $this->configureWebsiteId('2');
        $websiteDataMock = $this->createWebsiteDataModelMock('2');
        $this->configureCurrentWebsite($websiteDataMock);
        $html = $this->block->toHtml();

        self::assertContains('feefo-rating-service', $html);
        self::assertContains($widgetSnippet, $html);
    }


    /**
     * @return array
     */
    public function serviceWidgetProvider()
    {
        return [
            ['<script type=\"text/javascript\" id=\"feefo-plugin-widget-bootstrap\" src=\"//register-uat.feefo.com/api/ecommerce/plugin/widget/merchant/site-info\" async></script>\n'],
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
     * @return StorageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createWidgetSnippetStorage($widgetSnippets)
    {
        $storage = $this->basicMock(Storage::class);

        $storage->expects($this->any())
            ->method('getWidgetSnippets')
            ->willReturn($widgetSnippets);

        $storage->expects($this->any())
            ->method('getByStoreId')
            ->willReturn('test');

        return $storage;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|WidgetSnippetInterface
     */
    protected function createWidgetSnippets()
    {
        $widgetSnippets = $this->getMockBuilder(WidgetSnippetInterface::class)
            ->setMethods(['getServiceSnippet'])
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