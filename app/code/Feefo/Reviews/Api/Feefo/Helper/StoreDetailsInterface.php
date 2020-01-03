<?php

namespace Feefo\Reviews\Api\Feefo\Helper;

/**
 * interface StoreDetails
 *
 * Get information about store
 */
interface StoreDetailsInterface
{
    /**
     * Configure a scope for getting data
     *
     * @param array $data
     *
     * @return void
     */
    public function initScope($data);

    /**
     * Retrieve merchant domain
     *
     * @return string
     */
    public function getMerchantDomain();

    /**
     * Retrieve merchant name
     *
     * @return string
     */
    public function getMerchantName();

    /**
     * Retrieve merchant description
     *
     * @return string
     */
    public function getMerchantDescription();

    /**
     * Retrieve merchant URL
     *
     * @return mixed
     */
    public function getMerchantUrl();

    /**
     * Retrieve merchant language
     *
     * @return string
     */
    public function getMerchantLanguage();

    /**
     * Retrieve merchant email
     *
     * @return string
     */
    public function getMerchantEmail();

    /**
     * Retrieve merchant shop owner
     *
     * @return string
     */
    public function getMerchantShopOwner();

    /**
     * Retrieve merchant image URL
     *
     * @return string
     */
    public function getMerchantImageUrl();

    /**
     * Retrieve stores of a website with $websiteId
     *
     * @return string[]
     */
    public function getStoreIds();

    /**
     * Retrieve redirect URL
     *
     * @return string
     */
    public function getRedirectUrl();
}