<?php

namespace Feefo\Reviews\Test\Unit\Model\Feefo\Data;

use Feefo\Reviews\Model\Feefo\Data\JsonableDataObject;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Magento\Framework\Json\Helper\Data;

/**
 * Class JsonableDataObjectTest
 */
class JsonableDataObjectTest extends AbstractTestCase
{
    /**
     * Json Helper mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|Data
     */
    protected $jsonHelper;

    /**
     * JsonableDataObject mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|JsonableDataObject
     */
    protected $jsonableDataObjectMock;

    /**
     * JsonableDataObject
     *
     * @var JsonableDataObject
     */
    protected $jsonableDataObject;

    /**
     * SetUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->jsonHelper = $this->basicMock(Data::class);
        $data = [];
        $this->jsonableDataObjectMock = $this->basicMock(JsonableDataObject::class);
        $this->jsonableDataObject = $this->objectManager->getObject(JsonableDataObject::class, [
            'jsonHelper' => $this->jsonHelper,
            'data' => $data
        ]);
    }

    /**
     * Test setJSON method
     *
     * @return void
     */
    public function testSetJSON()
    {
        $this->jsonHelper->expects($this->once())
            ->method('jsonDecode')
            ->willReturn([1]);
        $result = $this->jsonableDataObject->setJSON('1');

        self::assertEquals(true, $result);
    }

    /**
     * Test asJSON method
     *
     * @return void
     */
    public function testAsJSON()
    {
        $this->jsonHelper->expects($this->once())
            ->method('jsonEncode')
            ->willReturn(true);
        $result = $this->jsonableDataObject->asJSON();

        self::assertEquals(true, $result);
    }

    /**
     * Test hasChanges method
     *
     * @return void
     */
    public function testHasChanges()
    {
        $this->jsonHelper->expects($this->once())
            ->method('jsonEncode')
            ->willReturn('1');
        $this->jsonableDataObjectMock->expects($this->once())
            ->method('asJSON')
            ->willReturn('1');
        $result = $this->jsonableDataObject->hasChanges($this->jsonableDataObjectMock);

        self::assertEquals(false, $result);
    }
}