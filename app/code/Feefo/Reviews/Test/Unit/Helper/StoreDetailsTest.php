<?php

namespace Feefo\Reviews\Test\Unit\Helper;

use Feefo\Reviews\Api\Feefo\Helper\ScopeInterface;
use Feefo\Reviews\Api\Feefo\StorageInterface;
use Feefo\Reviews\Helper\StoreDetails;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Magento\Authorization\Model\ResourceModel\Role\CollectionFactory;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Backend\Model\Url;
use Magento\Store\Model\WebsiteFactory;
use Magento\User\Model\ResourceModel\User as UserResource;
use Magento\User\Model\User;
use Magento\User\Model\UserFactory;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Psr\Log\LoggerInterface;

/**
 * Class StoreDetailsTest
 */
class StoreDetailsTest extends AbstractTestCase
{
    /**
     * Sample url
     */
    const TEST_URL = 'http://username:password@hostname:9090/path?arg=value#anchor';

    /**
     * Sample name
     */
    const TEST_NAME = 'test_name';

    /**
     * Sample path
     */
    const MERCHANT_DOMAIN = 'hostname/path';

    /**
     * @var StoreDetails
     */
    protected $storeDetails;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ScopeInterface
     */
    protected $scopeConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Url
     */
    protected $urlBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|User
     */
    protected $adminUserModel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    protected $logger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StoreDetails
     */
    protected $storeDetailsMock;

    /**
     * Admin login session
     *
     * @var AdminSession|Mock
     */
    protected $adminSessionMock;

    /**
     * Storage Mock
     *
     * @var StorageInterface|Mock
     */
    protected $storageMock;

    /**
     * SetUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->scopeConfig = $this->basicMock(ScopeInterface::class);
        $this->urlBuilder = $this->basicMock(Url::class);
        $this->adminUserModel = $this->basicMock(User::class);
        $this->adminSessionMock = $this->mixedMock(AdminSession::class, ['getUser']);
        $this->logger = $this->basicMock(LoggerInterface::class);
        $this->storeDetailsMock = $this->basicMock(StoreDetails::class);
        $this->storageMock = $this->basicMock(StorageInterface::class);

        $this->storeDetails = $this->objectManager->getObject(StoreDetails::class, [
            'scopeConfig' => $this->scopeConfig,
            'urlBuilder' => $this->urlBuilder,
            'logger' => $this->logger,
            'adminSession' => $this->adminSessionMock,
            'storage' => $this->storageMock,
        ]);
    }

    /**
     * Test getMerchantDomain method
     *
     * @return void
     */
    public function testGetMerchantDomain()
    {
        $expectedResult = 'hostname/path';
        $this->storageMock->expects($this->once())
            ->method('getWebsiteUrl')
            ->willReturn(self::TEST_URL);
        $merchantUrl = $this->storeDetails->getMerchantDomain();

        self::assertEquals($expectedResult, $merchantUrl);
    }

    /**
     * Test getMerchantName method
     *
     * @return void
     */
    public function testGetMerchantName()
    {
        $this->scopeConfig->expects($this->once())
            ->method('getConfig')
            ->willReturn(self::TEST_NAME);
        $merchantUrl = $this->storeDetails->getMerchantName();

        self::assertEquals(self::TEST_NAME, $merchantUrl);
    }

    /**
     * Test getMerchantDescription method
     *
     * @return void
     */
    public function testGetMerchantDescription()
    {
        $expectedResult = sprintf(StoreDetails::TEMPLATE_DEFAULT_MERCHANT_DESCRIPTION, self::MERCHANT_DOMAIN);
        $this->storageMock->expects($this->once())
            ->method('getWebsiteUrl')
            ->willReturn(self::TEST_URL);
        $merchantDescription = $this->storeDetails->getMerchantDescription();

        self::assertEquals($expectedResult, $merchantDescription);
    }

    /**
     * Test getMerchantUrl method
     *
     * @return void
     */
    public function testGetMerchantUrl()
    {
        $this->storageMock->expects($this->once())
            ->method('getWebsiteUrl')
            ->willReturn(self::TEST_URL);
        $merchantUrl = $this->storeDetails->getMerchantUrl();

        self::assertEquals(self::TEST_URL, $merchantUrl);
    }

    /**
     * Test getMerchantLanguage method
     *
     * @return void
     */
    public function testGetMerchantLanguage()
    {
        $this->scopeConfig->expects($this->once())
        ->method('getConfig')
        ->willReturn(null);
        $merchantLang = $this->storeDetails->getMerchantLanguage();

        self::assertEquals(StoreDetails::DEFAULT_MERCHANT_LANGUAGE, $merchantLang);
    }

    /**
     * Test getMerchantEmail method
     *
     * @return void
     */
    public function testGetMerchantEmail()
    {
        $testEmail = 'test@test.com';
        $this->runGetAdminUser();
        $this->adminUserModel->expects($this->once())->method('getId')->willReturn(1);
        $this->adminUserModel->expects($this->once())->method('getEmail')->willReturn($testEmail);

        self::assertEquals($testEmail, $this->storeDetails->getMerchantEmail());
    }

    /**
     * Test getMerchantEmail from config value
     *
     * @return void
     */
    public function testGetMerchantEmailDefaultConfig()
    {
        $testEmail = 'test@test.com';
        $this->adminSessionMock->expects($this->once())->method('getUser')->willReturn(null);
        $this->scopeConfig->expects($this->once())
            ->method('getConfig')
            ->with(StoreDetails::XPATH_GENERAL_EMAIL)
            ->willReturn($testEmail)
        ;
        self::assertEquals($testEmail, $this->storeDetails->getMerchantEmail());
    }

    /**
     * Test getMerchantEmail hardcoded value
     *
     * @return void
     */
    public function testGetMerchantEmailDefault()
    {
        $this->adminSessionMock->expects($this->once())->method('getUser')->willReturn(null);
        $this->scopeConfig->expects($this->once())
            ->method('getConfig')
            ->with(StoreDetails::XPATH_GENERAL_EMAIL)
            ->willReturn(null)
        ;
        self::assertEquals(StoreDetails::DEFAULT_MERCHANT_EMAIL, $this->storeDetails->getMerchantEmail());
    }

    /**
     * Test getMerchantShopOwner method
     *
     * @return void
     */
    public function testGetMerchantShopOwner()
    {
        $expectedResult = 'Some Name';
        $this->runGetAdminUser();
        $this->adminUserModel->expects($this->once())->method('getId')->willReturn(1);
        $this->adminUserModel->expects($this->once())->method('getName')->willReturn($expectedResult);

        $result = $this->storeDetails->getMerchantShopOwner();

        self::assertEquals($expectedResult, $result);
    }

    /**
     * Test getMerchantShopOwner method
     *
     * @return void
     */
    public function testGetMerchantShopOwnerDefault()
    {
        $expectedResult = sprintf(StoreDetails::TEMPLATE_DEFAULT_MERCHANT_NAME, self::MERCHANT_DOMAIN);
        $this->adminSessionMock->expects($this->once())->method('getUser')->willReturn(null);
        $this->storageMock->expects($this->once())
            ->method('getWebsiteUrl')
            ->willReturn(self::TEST_URL);
        $result = $this->storeDetails->getMerchantShopOwner();

        self::assertEquals($expectedResult, $result);
    }

    /**
     * Test getStoreIds method
     *
     * @return void
     */
    public function testGetStoreIds()
    {
        $expectedResult = '1,2,3';

        $this->storageMock->expects($this->once())->method('getStoreIds')->willReturn([1 , 2, 3]);

        $result = $this->storeDetails->getStoreIds(1);

        self::assertEquals($expectedResult, $result);
    }

    /**
     * Test getRedirectUrl method
     *
     * @return void
     */
    public function testGetRedirectUrl()
    {
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->willReturn(self::TEST_URL);
        $redirectUrl = $this->storeDetails->getRedirectUrl();

        self::assertEquals(self::TEST_URL, $redirectUrl);
    }

    /**
     * Simulate getAdminUser protected method call
     *
     * @return void
     */
    public function runGetAdminUser()
    {
        $this->adminSessionMock->expects($this->once())->method('getUser')->willReturn($this->adminUserModel);
    }
}
