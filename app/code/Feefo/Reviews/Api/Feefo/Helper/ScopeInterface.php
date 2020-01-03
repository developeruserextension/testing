<?php

namespace Feefo\Reviews\Api\Feefo\Helper;
use Magento\Store\Model\Website;

/**
* Interface ScopeInterface
*
* Encapsulates logic of gathering information from the all of the scopes
*/
interface ScopeInterface
{
    /**
     * Configure a scope for getting data
     *
     * @param array $data
     * 
     * @return void
     */
    public function initScope(array $data);

    /**
     * Retrieve the chosen website
     * 
     * @return Website
     */
    public function getWebsite();

    /**
     * Retrieve configured option from the storage for specific scope
     *
     * @param string $xpath
     * 
     * @return mixed
     */
    public function getConfig($xpath);
}