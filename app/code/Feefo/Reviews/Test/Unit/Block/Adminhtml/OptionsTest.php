<?php

namespace Feefo\Reviews\Test\Unit\Block\Adminhtml;

use Feefo\Reviews\Api\Feefo\Data\RegistrationRequestInterface;
use Feefo\Reviews\Api\Feefo\Data\StoreUrlGroupDataInterface;
use Feefo\Reviews\Api\Feefo\StoreUrlGroupInterface;
use Feefo\Reviews\Api\Feefo\WidgetInterface;
use Feefo\Reviews\Block\Adminhtml\Options;
use Feefo\Reviews\Helper\StoreDetails;
use Feefo\Reviews\Model\Feefo\Data\ConfigurationRequestFactory;
use Feefo\Reviews\Model\Feefo\Data\RegistrationRequest;
use Feefo\Reviews\Model\Feefo\Data\RegistrationRequestFactory;
use Feefo\Reviews\Model\Feefo\Data\Service;
use Feefo\Reviews\Model\Feefo\Data\WidgetWrapper;
use Feefo\Reviews\Model\Feefo\Registration;
use Feefo\Reviews\Model\Feefo\Storage;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Feefo\Reviews\Test\Unit\ObjectManagerFactory;
use Magento\Backend\Block\Template\Context as BackendTemplateContext;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\File\Resolver as FileResolver;
use Magento\Framework\View\Element\Template\File\Validator as TemplateFileValidator;
use Magento\Framework\View\TemplateEnginePool;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManager;

/**
 * Class OptionsTest
 */
class OptionsTest extends AbstractTestCase
{
    /**
     * @var Options
     */
    protected $block;

    /**
     * @var ObjectManagerInterface
     */
    protected $realObjectManager;

    /**
     * @var AppState
     */
    protected $appState;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StoreManager
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Registration
     */
    protected $registrationApiMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|WidgetInterface
     */
    protected $widgetApiMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StoreDetails
     */
    protected $storeDetailsMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RegistrationRequestFactory
     */
    protected $registrationRequestFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ConfigurationRequestFactory
     */
    protected $configRequestDataFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Storage
     */
    protected $storageMock;

    /**
     * @var RegistrationRequestInterface
     */
    protected $registrationRequestData;

    /**
     * @var Service
     */
    protected $responseData;

    /**
     * @var WidgetWrapper
     */
    protected $widgetWrapper;

    /**
     * Store Url Group Data
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|StoreUrlGroupDataInterface
     */
    protected $storeUrlGroupDataMock;

    /**
     * Store Url Group Data
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|StoreUrlGroupInterface
     */
    protected $storeUrlGroupMock;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->realObjectManager = $this->initRealObjectManager();
        $this->appState = $this->realObjectManager->get(AppState::class);
        $this->appState->setAreaCode(Area::AREA_ADMINHTML);

        $arguments = [
            'data' => []
        ];
        $context = $this->createTemplateContext();
        $this->storeManagerMock = $context->getStoreManager();

        $arguments['context'] = $context;
        $this->responseData = $this->createResponseData();
        $this->registrationApiMock = $arguments['registrationAPI'] = $this->createRegistrationApiMock($this->responseData);
        $this->widgetWrapper = $this->createWidgetWrapper();
        $this->widgetApiMock = $arguments['widgetAPI'] = $this->createWidgetApiMock($this->widgetWrapper);
        $this->storeDetailsMock = $arguments['storeDetails'] = $this->createStoreDetailsMock();
        $this->registrationRequestData = $this->createRegistrationRequestData();
        $this->registrationRequestFactoryMock = $arguments['registrationRequestFactory'] = $this->createRegistrationRequestFactoryMock($this->registrationRequestData);
        $this->configRequestDataFactoryMock = $arguments['configRequestDataFactory'] = $this->createConfigRequestDataFactoryMock($this->registrationRequestData);
        $this->storageMock = $arguments['storage'] = $this->createStorageMock();

        $this->storeUrlGroupDataMock = $this->basicMock(StoreUrlGroupDataInterface::class);
        $this->storeUrlGroupDataMock->expects($this->any())->method('getUrl')->willReturn('url');
        $this->storeUrlGroupMock = $this->basicMock(StoreUrlGroupInterface::class);
        $this->storeUrlGroupMock->expects($this->any())->method('getGroups')->willReturn([$this->storeUrlGroupDataMock]);
        $arguments['storeUrlGroup'] =  $this->storeUrlGroupMock;

        $this->block = $this->objectManager->getObject(Options::class, $arguments);
    }

    /**
     * @dataProvider websiteProvider
     * @param $websiteId
     * @param $websiteName
     * @return void
     */
    public function testChooseWebsiteState($websiteId, $websiteName)
    {
        $this->configureSelectedWebsiteId(null);
        $this->configureWebsites([
            $this->createWebsiteMock($websiteId, $websiteName)
        ]);

        $html = $this->block->toHtml();

        self::assertContains($websiteName, $html);
    }

    /**
     * @dataProvider websiteProvider
     * @param $websiteId
     * @param $websiteName
     * @return void
     */
    public function testChooseWebsiteLink($websiteId, $websiteName, $expected)
    {
        $this->configureSelectedWebsiteId(null);
        $this->configureWebsites([
            $this->createWebsiteMock($websiteId, $websiteName)
        ]);

        $html = $this->block->toHtml();

        self::assertContains($websiteName, $html);
    }

    /**
     * @dataProvider regDataProvider
     * @param string $jsonResponse
     * @param string $expected
     * @return void
     */
    public function testRegistrationState($jsonResponse, $expected)
    {
        $this->configureSelectedWebsiteId('13');
        $this->configureWebsite($this->createWebsiteMock('13', 'My website'));
        $this->configurePluginId(null);
        $this->configureRegistrationApiResponse($jsonResponse);
        $html = $this->block->getOptionsPageLink();

        self::assertContains($expected, $html);
    }

    /**
     * @dataProvider configDataProvider
     * @param string $jsonResponse
     * @param string $expected
     * @return void
     */
    public function testConfigurationState($jsonResponse, $expected)
    {
        $this->configureSelectedWebsiteId('13');
        $this->configureWebsite($this->createWebsiteMock('13', 'My website'));
        $this->configurePluginId('13bafg249fy74k4cii34ng');
        $this->configureRegistrationApiResponse($jsonResponse);
        $html = $this->block->getOptionsPageLink();

        self::assertContains($expected, $html);
    }

    /**
     * @dataProvider configDataProvider
     * @param string $jsonResponse
     * @return void
     */
    public function testErrorHandler($jsonResponse)
    {
        $this->configureSelectedWebsiteId('13');
        $this->configureWebsite($this->createWebsiteMock('13', 'My website'));
        $this->configurePluginId('13bafg249fy74k4cii34ng');
        $this->configureRegistrationApiResponse($jsonResponse);
        $this->registrationApiMock->expects($this->any())
            ->method('register')
            ->willThrowException(new \Exception('Test exception'));

        $html = $this->block->getOptionsPageLink();

        self::assertEmpty(trim($html));
    }

    /**
     * @return array
     */
    public function websiteProvider()
    {
        return [
            ['13', 'url', '/website_id/13/']
        ];
    }

    /**
     * @return array
     */
    public function regDataProvider()
    {
        return [
            ['{ "pluginId": "57f3af62e4b05ab241c24c5f", "registrationUrl": "https://register-uat.feefo.com/#/plugin/57f3af62e4b05ab241c24c5f/register" }', 'https://register-uat.feefo.com/#/plugin/57f3af62e4b05ab241c24c5f/register']
        ];
    }

    /**
     * @return array
     */
    public function configDataProvider()
    {
        return [
            ['{ "redirectUrl": "", "configurationUri": "https://register-uat.feefo.com/#/install/magento-v2/configuration/57f3af62e4b05ab241c24c5f?pluginId=57f3af62e4b05ab241c24c5f&host=demo.atw.com&timeStamp=20161004&hmac=c2fdec23186d04c3a2a3dff860359a10e13f94b467f68ffcb9b27464ab968876" }', 'https://register-uat.feefo.com/#/install/magento-v2/configuration/57f3af62e4b05ab241c24c5f?pluginId=57f3af62e4b05ab241c24c5f&host=demo.atw.com&timeStamp=20161004&hmac=c2fdec23186d04c3a2a3dff860359a10e13f94b467f68ffcb9b27464ab968876']
        ];
    }

    /**
     * @return ObjectManagerInterface
     */
    protected function initRealObjectManager()
    {
        $objectManagerFactory = new ObjectManagerFactory();
        $objectManager = $objectManagerFactory->create();
        $adminhtmlConfigurations = $objectManager
            ->get(ConfigLoader::class)
            ->load(Area::AREA_ADMINHTML);
        $objectManager->configure($adminhtmlConfigurations);

        return $objectManager;
    }

    /**
     * @return BackendTemplateContext
     */
    protected function createTemplateContext()
    {
        $contextArguments = $this->objectManager->getConstructArguments(BackendTemplateContext::class);
        $contextArguments['filesystem'] = $this->createFilesystemObject();
        $contextArguments['resolver'] = $this->createFileResolver();
        $contextArguments['validator'] = $this->createTemplateFileValidator();
        $contextArguments['enginePool'] = $this->createEnginePool();
        $contextArguments['urlBuilder'] = $this->createUrlBuilder();
        $contextArguments['storeManager'] = $this->createStoreManagerMock();

        return $this->objectManager->getObject(BackendTemplateContext::class, $contextArguments);
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
     * @return \PHPUnit_Framework_MockObject_MockObject|StoreManager
     */
    protected function createStoreManagerMock()
    {
        return $this->getMockBuilder(StoreManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getWebsites', 'getWebsite'])
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Registration
     */
    protected function createRegistrationApiMock($data)
    {
        $api = $this->getMockBuilder(Registration::class)
            ->disableOriginalConstructor()
            ->setMethods(['register'])
            ->getMock();

        $api->expects($this->any())
            ->method('register')
            ->willReturn($data);

        return $api;
    }

    /**
     * @param WidgetWrapper $wrapper
     * @return WidgetInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createWidgetApiMock($wrapper)
    {
        $api = $this->getMockBuilder(WidgetInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSettings'])
            ->getMock();

        $api->expects($this->any())
            ->method('getSettings')
            ->willReturn($wrapper);

        return $api;
    }

    /**
     * @param RegistrationRequestInterface $data
     * @return RegistrationRequestFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createRegistrationRequestFactoryMock($data)
    {
        $factory = $this->getMockBuilder(RegistrationRequestFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $factory->expects($this->any())
            ->method('create')
            ->willReturn($data);

        return $factory;
    }

    /**
     * @param RegistrationRequestInterface $data
     * @return ConfigurationRequestFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createConfigRequestDataFactoryMock($data)
    {
        $factory = $this->getMockBuilder(ConfigurationRequestFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $factory->expects($this->any())
            ->method('create')
            ->willReturn($data);

        return $factory;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|StoreDetails
     */
    protected function createStoreDetailsMock()
    {
        return $this->getMockBuilder(StoreDetails::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMerchantEmail'])
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Storage
     */
    protected function createStorageMock()
    {
        return $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getWebsiteId',
                'getWidgetSettings',
                'getWidgetSnippets',
                'setWidgetSettings',
                'setWidgetSnippets',
                'getPluginId',
                'setPluginId',
                'getWebsiteUrl',
            ])
            ->getMock();
    }

    /**
     * @param string|null $id
     */
    protected function configureSelectedWebsiteId($id)
    {
        $this->storageMock->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($id);
    }

    /**
     * @param [] $websites
     */
    protected function configureWebsites($websites)
    {
        $this->storeManagerMock->expects($this->any())
            ->method('getWebsites')
            ->willReturn($websites);
    }

    /**
     * @param string $websiteId
     * @param string $websiteName
     * @return \PHPUnit_Framework_MockObject_MockObject|WebsiteInterface
     */
    protected function createWebsiteMock($websiteId, $websiteName)
    {
        $websiteMock = $this->getMockBuilder(WebsiteInterface::class)
            ->setMethods(['getStoresCount', 'getStores', 'getWebsiteId', 'getName'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $websiteMock->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $websiteMock->expects($this->any())
            ->method('getName')
            ->willReturn($websiteName);

        $websiteMock->expects($this->any())
            ->method('getStoresCount')
            ->willReturn(1);

        $websiteMock->expects($this->any())
            ->method('getStores')
            ->willReturn([
                $this->createActiveStore()
            ]);

        return $websiteMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|StoreInterface
     */
    protected function createActiveStore()
    {
        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getIsActive'])
            ->getMockForAbstractClass();

        $storeMock->expects($this->any())
            ->method('getIsActive')
            ->willReturn(true);

        return $storeMock;
    }

    /**
     * @return UrlInterface
     */
    protected function createUrlBuilder()
    {
        return $this->realObjectManager->create(UrlInterface::class);
    }

    /**
     * @param WebsiteInterface
     */
    protected function configureWebsite($website)
    {
        $this->storeManagerMock->expects($this->any())
            ->method('getWebsite')
            ->willReturn($website);
    }

    /**
     * @param string|null $id
     */
    protected function configurePluginId($id)
    {
        $this->storageMock->expects($this->any())
            ->method('getPluginId')
            ->willReturn($id);
    }

    /**
     * @return RegistrationRequestInterface
     */
    protected function createRegistrationRequestData()
    {
        return $this->objectManager->getObject(RegistrationRequest::class);
    }

    /**
     * @return Service
     */
    protected function createResponseData()
    {
        return $this->realObjectManager->create(Service::class);
    }

    /**
     * @param string $jsonResponse
     */
    protected function configureRegistrationApiResponse($jsonResponse)
    {
        $this->responseData->setJSON($jsonResponse);
    }

    /**
     * @return WidgetWrapper
     */
    protected function createWidgetWrapper()
    {
        return $this->objectManager->getObject(WidgetWrapper::class);
    }

}