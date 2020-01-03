<?php

namespace Feefo\Reviews\Model\Feefo\Data;

use Feefo\Reviews\Api\Feefo\Data\JsonableInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class JsonableDataObject
 */
class JsonableDataObject extends DataObject implements JsonableInterface
{
    /** @var JsonHelper  */
    protected $jsonHelper;

    /**
     * JsonableDataObject constructor.
     * @param JsonHelper $jsonHelper
     * @param array $data
     */
    public function __construct(
        JsonHelper $jsonHelper,
        array $data = []
    ) {
        parent::__construct($data);

        $this->jsonHelper = $jsonHelper;
    }

    /**
     * Decode string and set as data
     *
     * @param string $jsonEncodedString
     * @return bool
     */
    public function setJSON($jsonEncodedString)
    {
        try {
            $decodedData = $this->jsonHelper->jsonDecode($jsonEncodedString);
            if (is_array($decodedData)) {
                $this->_data = $decodedData;

                return true;
            } else {
                return false;
            }
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Encode the data into the JSON format
     *
     * @return string
     */
    public function asJSON()
    {
        return $this->jsonHelper->jsonEncode($this->_data);
    }

    /**
     * Compare the data for the objects
     *
     * @param JsonableInterface $anotherObject
     * @return bool
     */
    public function hasChanges(JsonableInterface $anotherObject)
    {
        $currentJson = $this->asJSON();
        $anotherJson = $anotherObject->asJSON();

        return strcmp($currentJson, $anotherJson) !== 0;
    }

}