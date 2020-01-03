<?php

namespace Feefo\Reviews\Test\Unit;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    /**
     * Object Manager Instance
     *
     * @var ObjectManager object Manager
     */
    protected $objectManager;

    /**
     * SetUp
     *
     * @return void
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
    }

    /**
     * Build a basic (stub methods only) mock object
     *
     * @param string $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function basicMock($className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Build a full (mock methods only) mock object
     *
     * @param string $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function fullMock($className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
    }

    /**
     * Build a mixed (stub and mock methods) mock object
     *
     * @param string $className
     * @param array $methods
     * @param null|array $constructorArgs
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function mixedMock($className, array $methods, $constructorArgs = null)
    {
        if ($constructorArgs === null) {

            return $this->getMockBuilder($className)
                ->disableOriginalConstructor()
                ->setMethods($methods)
                ->getMock();
        } else {

            return $this->getMockBuilder($className)
                ->setConstructorArgs($constructorArgs)
                ->setMethods($methods)
                ->getMock();
        }
    }

    /**
     * Provides Boolean Data Array
     *
     * @return array
     */
    public function booleanDataProvider()
    {
        return [[true], [false]];
    }
}
