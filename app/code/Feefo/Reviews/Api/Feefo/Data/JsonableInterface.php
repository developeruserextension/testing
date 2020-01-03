<?php

namespace Feefo\Reviews\Api\Feefo\Data;

/**
 * Interface JsonableInterface
 *
 * Provide interface for classes can be converted to JSON
 */
interface JsonableInterface
{
    /**
     * Decode string and set as data
     *
     * @param string $jsonEncodedString
     * @return bool
     */
    public function setJSON($jsonEncodedString);

    /**
     * Encode the data into the JSON format
     *
     * @return string
     */
    public function asJSON();

    /**
     * Compare the data for the objects
     *
     * @param JsonableInterface $anotherObject
     * @return bool
     */
    public function hasChanges(JsonableInterface $anotherObject);
}