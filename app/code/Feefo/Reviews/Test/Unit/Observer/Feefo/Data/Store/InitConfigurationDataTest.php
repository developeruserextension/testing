<?php

namespace Feefo\Reviews\Test\Unit\Observer\Feefo\Data\Store;

use Feefo\Reviews\Api\Feefo\Helper\StoreDetailsInterface;
use Feefo\Reviews\Model\Feefo\Data\ConfigurationRequest;
use Feefo\Reviews\Model\Feefo\Storage;
use Feefo\Reviews\Observer\Feefo\Data\Store\InitConfigurationData;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;

/**
 * Class InitConfigurationDataTest
 */
class InitConfigurationDataTest extends AbstractTestCase
{
    /**
     * @var InitConfigurationData
     */
    protected $observer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Storage
     */
    protected $storage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StoreDetailsInterface
     */
    protected $storeDetails;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $arguments = $this->objectManager->getConstructArguments(InitConfigurationData::class);
        $this->storage = $arguments['storage'];
        $this->storeDetails = $arguments['storeDetails'];
        $this->observer = $this->objectManager->getObject(InitConfigurationData::class, $arguments);
    }

    /**
     * @dataProvider configProvider
     * @param $pluginId
     * @param $redirectUrl
     * 
     * @return void
     */
    public function testExecute($pluginId, $redirectUrl)
    {
        $this->storage->expects($this->any())
            ->method('getPluginId')
            ->willReturn($pluginId);

        $this->storeDetails->expects($this->any())
            ->method('getRedirectUrl')
            ->willReturn($redirectUrl);

        $configurationRequest = $this->getConfigurationRequestObject();
        $event = $this->getEventObject([
            'data' => $configurationRequest
        ]);
        $observerObject = $this->getObserverObject();
        $observerObject->setEvent($event);
        $this->observer->execute($observerObject);

        self::assertEquals($pluginId, $configurationRequest->getPluginId());
        self::assertEquals($redirectUrl, $configurationRequest->getRedirectUrl());
    }

    /**
     * @return array
     */
    public function configProvider()
    {
        return [
            ['abc13', 'http://feefo.com']
        ];
    }

    /**
     * @return Observer
     */
    protected function getObserverObject()
    {
        return $this->objectManager->getObject(Observer::class);
    }

    /**
     * @return ConfigurationRequest
     */
    protected function getConfigurationRequestObject()
    {
        return $this->objectManager->getObject(ConfigurationRequest::class);
    }

    /**
     * @param array $data
     * @return Event
     */
    protected function getEventObject($data = [])
    {
        return $this->objectManager->getObject(Event::class, [
            'data' => $data,
        ]);
    }

}
