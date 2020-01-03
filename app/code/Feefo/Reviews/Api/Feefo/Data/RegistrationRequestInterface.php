<?php

namespace Feefo\Reviews\Api\Feefo\Data;

/**
 * Interface RegistrationRequestInterface
 *
 * Data Service Contract that describes store info for Feefo service
 */
interface RegistrationRequestInterface
{
    /**
     * Available data
     */
    const ACCESS_TOKEN = 'accessToken';

    const MERCHANT_DOMAIN = 'merchantDomain';

    const MERCHANT_NAME = 'merchantName';

    const MERCHANT_DESCRIPTION = 'merchantDescription';

    const MERCHANT_URL = 'merchantUrl';

    const MERCHANT_LANGUAGE = 'merchantLanguage';

    const MERCHANT_ADMIN_USER_EMAIL = 'merchantAdminUserEmail';

    const MERCHANT_SHOP_OWNER = 'merchantShopOwner';

    const MERCHANT_IMAGE_URL = 'merchantImageUrl';

    const STORE_IDS = 'storeIds';

    const REDIRECT_URL = 'redirectUrl';

    /**
     * Get access token
     *
     * @return string
     */
    public function getAccessToken();

    /**
     * Set access token
     *
     * @param string $token
     * @return $this
     */
    public function setAccessToken($token);

    /**
     * Get merchant domain
     *
     * @return string
     */
    public function getMerchantDomain();

    /**
     * Set merchant domain
     *
     * @param string $domain
     * @return $this
     */
    public function setMerchantDomain($domain);

    /**
     * Get merchant name
     *
     * @return string
     */
    public function getMerchantName();

    /**
     * Set merchant name
     *
     * @param string $name
     * @return $this
     */
    public function setMerchantName($name);

    /**
     * Get merchant description
     *
     * @return string
     */
    public function getMerchantDescription();

    /**
     * Set merchant description
     *
     * @param string $description
     * @return $this
     */
    public function setMerchantDescription($description);

    /**
     * Get merchant url
     *
     * @return string
     */
    public function getMerchantUrl();

    /**
     * Set merchant url
     *
     * @param string $url
     * @return $this
     */
    public function setMerchantUrl($url);

    /**
     * Get merchant language
     *
     * @return string
     */
    public function getMerchantLanguage();

    /**
     * Set merchant language
     *
     * @param string $lang
     * @return $this
     */
    public function setMerchantLanguage($lang);

    /**
     * Get merchant email
     *
     * @return string
     */
    public function getMerchantEmail();

    /**
     * Set merchant email
     *
     * @param string $email
     * @return $this
     */
    public function setMerchantEmail($email);

    /**
     * Get merchant shop owner
     *
     * @return string
     */
    public function getMerchantShopOwner();

    /**
     * Set merchant shop owner
     *
     * @param string $owner
     * @return $this
     */
    public function setMerchantShopOwner($owner);

    /**
     * Get merchant image url
     *
     * @return string
     */
    public function getMerchantImageUrl();

    /**
     * Set merchant image url
     *
     * @param string $url
     * @return $this
     */
    public function setMerchantImageUrl($url);

    /**
     * Get store ids
     *
     * @return string
     */
    public function getStoreIds();

    /**
     * Set store ids
     *
     * @param string $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds);

    /**
     * Get redirect url
     *
     * @return string
     */
    public function getRedirectUrl();

    /**
     * Set redirect url
     *
     * @param string $redirectUrl
     * @return $this
     */
    public function setRedirectUrl($redirectUrl);
}