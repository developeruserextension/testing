<?php

namespace Feefo\Reviews\Test\Unit\Model\Feefo\Data;

use Feefo\Reviews\Model\Feefo\Data\Service;
use Feefo\Reviews\Test\Unit\AbstractTestCase;

/**
 * Class ServiceTest
 */
class ServiceTest extends AbstractTestCase
{
    /**
     * Sample Value
     */
    const SAMPLE_VALUE = 'value';

    /**
     * Service
     *
     * @var Service
     */
    protected $service;

    /**
     * SetUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->service = $this->objectManager->getObject(Service::class);

        $this->service->setData([
            Service::PLUGIN_ID => self::SAMPLE_VALUE,
            Service::REGISTRATION_URI => self::SAMPLE_VALUE
        ]);
    }

    /**
     * Test getPluginId method
     *
     * @return void
     */
    public function testGetPluginId()
    {
        $result = $this->service->getPluginId();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

    /**
     * Test getPageUrl method
     *
     * @return void
     */
    public function testGetPageUrl()
    {
        $result = $this->service->getPageUrl();

        self::assertEquals(self::SAMPLE_VALUE, $result);
    }

}