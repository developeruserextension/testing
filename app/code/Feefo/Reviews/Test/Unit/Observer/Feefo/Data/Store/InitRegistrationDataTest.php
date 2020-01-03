<?php

namespace Feefo\Reviews\Test\Unit\Observer\Feefo\Data\Store;

use Feefo\Reviews\Api\Feefo\Helper\StoreDetailsInterface;
use Feefo\Reviews\Model\Feefo\Data\RegistrationRequest;
use Feefo\Reviews\Observer\Feefo\Data\Store\InitRegistrationData;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;

/**
 * Class InitRegistrationDataTest
 */
class InitRegistrationDataTest extends AbstractTestCase
{
    /**
     * @var InitRegistrationData
     */
    protected $observer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StoreDetailsInterface
     */
    protected $storeDetails;

    /**
     * @var RegistrationRequest
     */
    protected $registrationData;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $arguments = $this->objectManager->getConstructArguments(InitRegistrationData::class);
        $this->storeDetails = $arguments['storeDetails'];
        $this->observer = $this->objectManager->getObject(InitRegistrationData::class, $arguments);
        $this->registrationData = $this->createRegistrationData();
    }

    /**
     * @dataProvider websiteProvider
     * @param string $websiteId
     * @param string[] $storeIds
     * @return void
     */
    public function testWebsiteAssignment($websiteId, $storeIds)
    {
        $this->storeDetails->expects($this->any())
            ->method('getStoreIds')
            ->willReturn($storeIds);

        $event = $this->getEventObject([
            'data' => $this->registrationData,
            'website_id' => $websiteId
        ]);
        $observerObject = $this->getObserverObject();
        $observerObject->setEvent($event);
        $this->observer->execute($observerObject);

        self::assertEquals($storeIds, $this->registrationData->getStoreIds());
    }

    /**
     * @dataProvider domainProvider
     * @param string $domain
     * @return void
     */
    public function testDomainAssignment($domain)
    {
        $this->storeDetails->expects($this->any())
            ->method('getMerchantDomain')
            ->willReturn($domain);

        $event = $this->getEventObject([
            'data' => $this->registrationData,
            'website_id' => '13'
        ]);
        $observerObject = $this->getObserverObject();
        $observerObject->setEvent($event);
        $this->observer->execute($observerObject);

        self::assertEquals($domain, $this->registrationData->getMerchantDomain());
    }

    /**
     * @dataProvider urlProvider
     * @param string $url
     * @return void
     */
    public function testMerchantUrlAssignment($url)
    {
        $this->storeDetails->expects($this->any())
            ->method('getMerchantUrl')
            ->willReturn($url);

        $event = $this->getEventObject([
            'data' => $this->registrationData,
            'website_id' => '13'
        ]);
        $observerObject = $this->getObserverObject();
        $observerObject->setEvent($event);
        $this->observer->execute($observerObject);

        self::assertEquals($url, $this->registrationData->getMerchantUrl());
    }

    /**
     * @dataProvider emailProvider
     * @param string $email
     * @return void
     */
    public function testMerchantEmailAssignment($email)
    {
        $this->storeDetails->expects($this->any())
            ->method('getMerchantEmail')
            ->willReturn($email);

        $event = $this->getEventObject([
            'data' => $this->registrationData,
            'website_id' => '13'
        ]);
        $observerObject = $this->getObserverObject();
        $observerObject->setEvent($event);
        $this->observer->execute($observerObject);

        self::assertEquals($email, $this->registrationData->getMerchantEmail());
    }

    /**
     * @dataProvider nameProvider
     * @param string $name
     * @return void
     */
    public function testMerchantNameAssignment($name)
    {
        $this->storeDetails->expects($this->any())
            ->method('getMerchantName')
            ->willReturn($name);

        $event = $this->getEventObject([
            'data' => $this->registrationData,
            'website_id' => '13'
        ]);
        $observerObject = $this->getObserverObject();
        $observerObject->setEvent($event);
        $this->observer->execute($observerObject);

        self::assertEquals($name, $this->registrationData->getMerchantName());
    }

    /**
     * @dataProvider languageProvider
     * @param string $lang
     * @return void
     */
    public function testMerchantLanguageAssignment($lang)
    {
        $this->storeDetails->expects($this->any())
            ->method('getMerchantLanguage')
            ->willReturn($lang);

        $event = $this->getEventObject([
            'data' => $this->registrationData,
            'website_id' => '13'
        ]);
        $observerObject = $this->getObserverObject();
        $observerObject->setEvent($event);
        $this->observer->execute($observerObject);

        self::assertEquals($lang, $this->registrationData->getMerchantLanguage());
    }

    /**
     * @dataProvider ownerProvider
     * @param string $owner
     * @return void
     */
    public function testMerchantShopOwnerAssignment($owner)
    {
        $this->storeDetails->expects($this->any())
            ->method('getMerchantShopOwner')
            ->willReturn($owner);

        $event = $this->getEventObject([
            'data' => $this->registrationData,
            'website_id' => '13'
        ]);
        $observerObject = $this->getObserverObject();
        $observerObject->setEvent($event);
        $this->observer->execute($observerObject);

        self::assertEquals($owner, $this->registrationData->getMerchantShopOwner());
    }

    /**
     * @dataProvider descriptionProvider
     * @param string $description
     * @return void
     */
    public function testMerchantDescriptionAssignment($description)
    {
        $this->storeDetails->expects($this->any())
            ->method('getMerchantDescription')
            ->willReturn($description);

        $event = $this->getEventObject([
            'data' => $this->registrationData,
            'website_id' => '13'
        ]);
        $observerObject = $this->getObserverObject();
        $observerObject->setEvent($event);
        $this->observer->execute($observerObject);

        self::assertEquals($description, $this->registrationData->getMerchantDescription());
    }

    /**
     * @dataProvider urlProvider
     * @param string $url
     * @return void
     */
    public function testRedirectUrlAssignment($url)
    {
        $this->storeDetails->expects($this->any())
            ->method('getRedirectUrl')
            ->willReturn($url);

        $event = $this->getEventObject([
            'data' => $this->registrationData,
            'website_id' => '13'
        ]);
        $observerObject = $this->getObserverObject();
        $observerObject->setEvent($event);
        $this->observer->execute($observerObject);

        self::assertEquals($url, $this->registrationData->getRedirectUrl());
    }

    /**
     * @return array
     */
    public function websiteProvider()
    {
        return [
            ['1', ['13', '101']]
        ];
    }

    /**
     * @return array
     */
    public function domainProvider()
    {
        return [
            ['demo.feefo.com'],
            ['atw.feefo.com'],
        ];
    }

    /**
     * @return array
     */
    public function urlProvider()
    {
        return [
            ['http://demo.feefo.com'],
            ['https://atw.feefo.com'],
        ];
    }

    /**
     * @return array
     */
    public function emailProvider()
    {
        return [
            ['test@feefo.com'],
            ['tech@site.com'],
        ];
    }

    /**
     * @return array
     */
    public function nameProvider()
    {
        return [
            ['Merchant Test Name'],
            ['Outdoor Great Company'],
        ];
    }

    /**
     * @return array
     */
    public function languageProvider()
    {
        return [
            ['ENG'],
            ['UA'],
        ];
    }

    /**
     * @return array
     */
    public function ownerProvider()
    {
        return [
            ['Katz Isaak'],
            ['Roman Hlushko'],
        ];
    }

    /**
     * @return array
     */
    public function descriptionProvider()
    {
        return [
            ['The best webstore ever!'],
            ['2 EUR store. Everything for 2 EUR only!'],
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
     * @param array $data
     * @return Event
     */
    protected function getEventObject($data = [])
    {
        return $this->objectManager->getObject(Event::class, [
            'data' => $data,
        ]);
    }

    /**
     * @return RegistrationRequest
     */
    protected function createRegistrationData()
    {
        return $this->objectManager->getObject(RegistrationRequest::class);
    }

    /**
     * @param string $needle
     * @param string[] $array
     */
    protected static function assertInArray($needle, $array)
    {
        $isInArray = in_array($needle, $array);
        self::assertTrue($isInArray);
    }
}
