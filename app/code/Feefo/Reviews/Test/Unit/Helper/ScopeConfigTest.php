<?php

namespace Feefo\Reviews\Test\Unit\Helper;

use Feefo\Reviews\Helper\ScopeConfig;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website;
use Magento\Store\Model\WebsiteFactory;

/**
 * Class ScopeConfigTest
 */
class ScopeConfigTest extends AbstractTestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|ScopeConfigInterface */
    protected $scopeConfigInterface;

    /** @var \PHPUnit_Framework_MockObject_MockObject|WebsiteFactory */
    protected $websiteFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Website */
    protected $website = null;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ScopeConfig */
    protected $scopeConfigMock;

    /** @var ScopeConfig */
    protected $scopeConfig;

    /**
     * SetUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $arguments = $this->objectManager->getConstructArguments(ScopeConfig::class);
        $this->scopeConfigInterface = $arguments['scopeConfig'];
        $this->websiteFactory = $this->mixedMock(WebsiteFactory::class, ['create']);
        $arguments['websiteFactory'] = $this->websiteFactory;
        $this->website = $this->basicMock(Website::class);
        $this->scopeConfig = $this->objectManager->getObject(ScopeConfig::class, $arguments);
        $this->setBackwardCompatibleProperty($this->scopeConfig, 'website', $this->website);
    }

    /**
     * Test getConfig method
     *
     * @return void
     */
    public function testGetConfig()
    {
        $storeMock = $this->basicMock(Store::class);
        $this->website->expects($this->exactly(3))
            ->method('getId')
            ->willReturn(1);
        $this->scopeConfigInterface->expects($this->exactly(3))
            ->method('getValue')
            ->willReturn(false);
        $this->website->expects($this->once())
            ->method('getDefaultStore')
            ->willReturn($storeMock);
        $storeMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1);
        $result = $this->scopeConfig->getConfig('path');

        self::assertEquals(false, $result);
    }

    /**
     * Test initScope method
     *
     * @return void
     */
    public function testInitScope()
    {
        $data = [
            'website_id' => 1
        ];
        $this->website->expects($this->once())
        ->method('load')
        ->willReturn($this->website);
        $this->scopeConfig->initScope($data);
    }

    /**
     * Set mocked property
     *
     * @param object $object
     * @param string $propertyName
     * @param object $propertyValue
     * @return void
     */
    public function setBackwardCompatibleProperty($object, $propertyName, $propertyValue)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $reflectionProperty = $reflection->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $propertyValue);
    }
}
