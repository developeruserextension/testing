<?php

namespace Feefo\Reviews\Model\Feefo\Data;

use Feefo\Reviews\Api\Feefo\Data\RegistrationRequestInterface;

/**
 * Class RegistrationRequest
 */
class RegistrationRequest extends JsonableDataObject implements RegistrationRequestInterface
{
    /**
     * Get access token
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->getData(static::ACCESS_TOKEN);
    }

    /**
     * Set access token
     *
     * @param string $token
     * @return $this
     */
    public function setAccessToken($token)
    {
        return $this->setData(static::ACCESS_TOKEN, $token);
    }

    /**
     * Get merchant domain
     *
     * @return string
     */
    public function getMerchantDomain()
    {
        return $this->getData(static::MERCHANT_DOMAIN);
    }

    /**
     * Set merchant domain
     *
     * @param string $domain
     * @return $this
     */
    public function setMerchantDomain($domain)
    {
        $this->setData(static::MERCHANT_DOMAIN, $domain);
    }

    /**
     * Get merchant description
     *
     * @return string
     */
    public function getMerchantDescription()
    {
        return $this->getData(static::MERCHANT_DESCRIPTION);
    }

    /**
     * Set merchant description
     *
     * @param string $description
     * @return $this
     */
    public function setMerchantDescription($description)
    {
        return $this->setData(static::MERCHANT_DESCRIPTION, $description);
    }

    /**
     * Get merchant url
     *
     * @return string
     */
    public function getMerchantUrl()
    {
        return $this->getData(static::MERCHANT_URL);
    }

    /**
     * Set merchant url
     *
     * @param string $url
     * @return $this
     */
    public function setMerchantUrl($url)
    {
        return $this->setData(static::MERCHANT_URL, $url);
    }

    /**
     * Get merchant language
     *
     * @return string
     */
    public function getMerchantLanguage()
    {
        return $this->getData(static::MERCHANT_LANGUAGE);
    }

    /**
     * Set merchant language
     *
     * @param string $lang
     * @return $this
     */
    public function setMerchantLanguage($lang)
    {
        return $this->setData(static::MERCHANT_LANGUAGE, $lang);
    }

    /**
     * Get merchant email
     *
     * @return string
     */
    public function getMerchantEmail()
    {
        return $this->getData(static::MERCHANT_ADMIN_USER_EMAIL);
    }

    /**
     * Set merchant email
     *
     * @param string $email
     * @return $this
     */
    public function setMerchantEmail($email)
    {
        return $this->setData(static::MERCHANT_ADMIN_USER_EMAIL, $email);
    }

    /**
     * Get merchant shop owner
     *
     * @return string
     */
    public function getMerchantShopOwner()
    {
        return $this->getData(static::MERCHANT_SHOP_OWNER);
    }

    /**
     * Set merchant shop owner
     *
     * @param string $owner
     * @return $this
     */
    public function setMerchantShopOwner($owner)
    {
        return $this->setData(static::MERCHANT_SHOP_OWNER, $owner);
    }

    /**
     * Get merchant image url
     *
     * @return string
     */
    public function getMerchantImageUrl()
    {
        return $this->getData(static::MERCHANT_IMAGE_URL);
    }

    /**
     * Set merchant image url
     *
     * @param string $url
     * @return $this
     */
    public function setMerchantImageUrl($url)
    {
        return $this->setData(static::MERCHANT_IMAGE_URL, $url);
    }

    /**
     * Get merchant name
     *
     * @return string
     */
    public function getMerchantName()
    {
        return $this->getData(static::MERCHANT_NAME);
    }

    /**
     * Set merchant name
     *
     * @param string $name
     * @return $this
     */
    public function setMerchantName($name)
    {
        return $this->setData(static::MERCHANT_NAME, $name);
    }

    /**
     * Get store ids
     *
     * @return string
     */
    public function getStoreIds()
    {
        return $this->getData(static::STORE_IDS);
    }

    /**
     * Set store ids
     *
     * @param string $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        return $this->setData(static::STORE_IDS, $storeIds);
    }

    /**
     * Get redirect url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getData(static::REDIRECT_URL);
    }

    /**
     * Set redirect url
     *
     * @param string $redirectUrl
     * @return $this
     */
    public function setRedirectUrl($redirectUrl)
    {
        return $this->setData(static::REDIRECT_URL, $redirectUrl);
    }
}