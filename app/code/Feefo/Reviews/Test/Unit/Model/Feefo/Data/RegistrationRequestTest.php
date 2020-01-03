<?php

namespace Feefo\Reviews\Test\Unit\Model\Feefo\Data;

use Feefo\Reviews\Model\Feefo\Data\RegistrationRequest;
use Feefo\Reviews\Test\Unit\AbstractTestCase;

/**
 * Class RegistrationRequestTest
 */
class RegistrationRequestTest extends AbstractTestCase
{
    /**
     * Sample Value
     */
    const SAMPLE_VALUE = 'value';

    /**
     * RegistrationRequest
     *
     * @var RegistrationRequest
     */
    protected $registrationRequest;

    /**
     * SetUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->registrationRequest = $this->objectManager->getObject(RegistrationRequest::class);
        $this->registrationRequest->setData([
            RegistrationRequest::ACCESS_TOKEN => self::SAMPLE_VALUE,
            RegistrationRequest::MERCHANT_DESCRIPTION => self::SAMPLE_VALUE,
            RegistrationRequest::MERCHANT_URL => self::SAMPLE_VALUE,
            RegistrationRequest::MERCHANT_LANGUAGE => self::SAMPLE_VALUE,
            RegistrationRequest::MERCHANT_ADMIN_USER_EMAIL => self::SAMPLE_VALUE,
            RegistrationRequest::MERCHANT_LANGUAGE => self::SAMPLE_VALUE,
            RegistrationRequest::MERCHANT_SHOP_OWNER => self::SAMPLE_VALUE,
            RegistrationRequest::MERCHANT_IMAGE_URL => self::SAMPLE_VALUE,
            RegistrationRequest::MERCHANT_NAME => self::SAMPLE_VALUE,
            RegistrationRequest::STORE_IDS => self::SAMPLE_VALUE,
            RegistrationRequest::REDIRECT_URL => self::SAMPLE_VALUE,
            RegistrationRequest::MERCHANT_DOMAIN => self::SAMPLE_VALUE
        ]);
    }

    /**
     * Test getAccessToken method
     *
     * @return void
     */
    public function testGetAccessToken()
    {
        $result = $this->registrationRequest->getAccessToken();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setAccessToken method
     *
     * @return void
     */
    public function testSetAccessToken()
    {
        $this->registrationRequest->setAccessToken(self::SAMPLE_VALUE);
        $result = $this->registrationRequest->getData(RegistrationRequest::ACCESS_TOKEN);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getMerchantDomain method
     *
     * @return void
     */
    public function testGetMerchantDomain()
    {
        $result = $this->registrationRequest->getMerchantDomain();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setMerchantDomain method
     *
     * @return void
     */
    public function testSetMerchantDomain()
    {
        $this->registrationRequest->setMerchantDomain(self::SAMPLE_VALUE);
        $result = $this->registrationRequest->getData(RegistrationRequest::MERCHANT_DOMAIN);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getMerchantDescription method
     *
     * @return void
     */
    public function testGetMerchantDescription()
    {
        $result = $this->registrationRequest->getMerchantDescription();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setMerchantDescription method
     *
     * @return void
     */
    public function testSetMerchantDescription()
    {
        $this->registrationRequest->setMerchantDescription(self::SAMPLE_VALUE);
        $result = $this->registrationRequest->getData(RegistrationRequest::MERCHANT_DESCRIPTION);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getMerchantUrl method
     *
     * @return void
     */
    public function testGetMerchantUrl()
    {
        $result = $this->registrationRequest->getMerchantUrl();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setMerchantUrl method
     *
     * @return void
     */
    public function testSetMerchantUrl()
    {
        $this->registrationRequest->setMerchantUrl(self::SAMPLE_VALUE);
        $result = $this->registrationRequest->getData(RegistrationRequest::MERCHANT_URL);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getMerchantLanguage method
     *
     * @return void
     */
    public function testGetMerchantLanguage()
    {
        $result = $this->registrationRequest->getMerchantLanguage();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setMerchantLanguage method
     *
     * @return void
     */
    public function testSetMerchantLanguage()
    {
        $this->registrationRequest->setMerchantLanguage(self::SAMPLE_VALUE);
        $result = $this->registrationRequest->getData(RegistrationRequest::MERCHANT_LANGUAGE);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getMerchantEmail method
     *
     * @return void
     */
    public function testGetMerchantEmail()
    {
        $result = $this->registrationRequest->getMerchantEmail();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setMerchantEmail method
     *
     * @return void
     */
    public function testSetMerchantEmail()
    {
        $this->registrationRequest->setMerchantEmail(self::SAMPLE_VALUE);
        $result = $this->registrationRequest->getData(RegistrationRequest::MERCHANT_ADMIN_USER_EMAIL);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getMerchantShopOwner method
     *
     * @return void
     */
    public function testGetMerchantShopOwner()
    {
        $result = $this->registrationRequest->getMerchantShopOwner();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setMerchantShopOwner method
     *
     * @return void
     */
    public function testSetMerchantShopOwner()
    {
        $this->registrationRequest->setMerchantShopOwner(self::SAMPLE_VALUE);
        $result = $this->registrationRequest->getData(RegistrationRequest::MERCHANT_SHOP_OWNER);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getMerchantImageUrl method
     *
     * @return void
     */
    public function testGetMerchantImageUrl()
    {
        $result = $this->registrationRequest->getMerchantImageUrl();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setMerchantImageUrl method
     *
     * @return void
     */
    public function testSetMerchantImageUrl()
    {
        $this->registrationRequest->setMerchantImageUrl(self::SAMPLE_VALUE);
        $result = $this->registrationRequest->getData(RegistrationRequest::MERCHANT_IMAGE_URL);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getMerchantName method
     *
     * @return void
     */
    public function testGetMerchantName()
    {
        $result = $this->registrationRequest->getMerchantName();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setMerchantName method
     *
     * @return void
     */
    public function testSetMerchantName()
    {
        $this->registrationRequest->setMerchantName(self::SAMPLE_VALUE);
        $result = $this->registrationRequest->getData(RegistrationRequest::MERCHANT_NAME);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getStoreIds method
     *
     * @return void
     */
    public function testGetStoreIds()
    {
        $result = $this->registrationRequest->getStoreIds();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setStoreIds method
     *
     * @return void
     */
    public function testSetStoreIds()
    {
        $this->registrationRequest->setStoreIds(self::SAMPLE_VALUE);
        $result = $this->registrationRequest->getData(RegistrationRequest::STORE_IDS);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getRedirectUrl method
     *
     * @return void
     */
    public function testGetRedirectUrl()
    {
        $result = $this->registrationRequest->getRedirectUrl();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setRedirectUrl method
     *
     * @return void
     */
    public function testSetRedirectUrl()
    {
        $this->registrationRequest->setRedirectUrl(self::SAMPLE_VALUE);
        $result = $this->registrationRequest->getData(RegistrationRequest::REDIRECT_URL);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }
}