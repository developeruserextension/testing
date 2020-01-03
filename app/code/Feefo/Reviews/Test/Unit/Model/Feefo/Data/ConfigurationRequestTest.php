<?php

namespace Feefo\Reviews\Test\Unit\Model\Feefo\Data;

use Feefo\Reviews\Model\Feefo\Data\ConfigurationRequest;
use Feefo\Reviews\Test\Unit\AbstractTestCase;

/**
 * Class ScopeConfigTest
 */
class ScopeConfigTest extends AbstractTestCase
{
    /**
     * Sample Value
     */
    const SAMPLE_VALUE = 'value';

    /**
     * ConfigurationRequest
     *
     * @var ConfigurationRequest
     */
    protected $configurationRequest;

    /**
     * SetUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->configurationRequest = $this->objectManager->getObject(ConfigurationRequest::class);
        $this->configurationRequest->setData([
            ConfigurationRequest::PLUGIN_ID => self::SAMPLE_VALUE,
            ConfigurationRequest::REDIRECT_URL => self::SAMPLE_VALUE
        ]);
    }

    /**
     * Test getPluginId method
     *
     * @return void
     */
    public function testGetPluginId()
    {
        $result = $this->configurationRequest->getPluginId();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setPluginId method
     *
     * @return void
     */
    public function testSetPluginId()
    {
        $this->configurationRequest->setPluginId(self::SAMPLE_VALUE);
        $result = $this->configurationRequest->getData(ConfigurationRequest::PLUGIN_ID);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getRedirectUrl method
     *
     * @return void
     */
    public function testGetRedirectUrl()
    {
        $result = $this->configurationRequest->getRedirectUrl();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test setRedirectUrl method
     *
     * @return void
     */
    public function testSetRedirectUrl()
    {
        $this->configurationRequest->setRedirectUrl(self::SAMPLE_VALUE);
        $result = $this->configurationRequest->getData(ConfigurationRequest::REDIRECT_URL);

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }
}