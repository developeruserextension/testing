<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Model;

class ConfigProvider extends \Amasty\Base\Model\ConfigProviderAbstract
{
    protected $pathPrefix = 'amstorecredit/';

    /**#@+
     * Constants defined for xpath of system configuration
     */
    const XPATH_ENABLED = 'general/enabled';
    const REFUND_AUTOMATICALLY = 'general/refund_automatically';
    const ALLOW_ON_TAX = 'general/allow_on_tax';
    const ALLOW_ON_SHIPPING = 'general/allow_on_shipping';

    const EMAIL_ENABLED = 'email/enabled';
    const EMAIL_ACTIONS = 'email/actions';
    const EMAIL_SENDER = 'email/sender';
    const EMAIL_REPLY = 'email/reply';
    const EMAIL_TEMPLATE = 'email/template';
    /**#@-*/

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isSetFlag(self::XPATH_ENABLED);
    }

    /**
     * @return bool
     */
    public function isRefundAutomatically()
    {
        return $this->isSetFlag(self::REFUND_AUTOMATICALLY);
    }

    /**
     * @var int $storeId
     *
     * @return bool
     */
    public function isAllowOnTax($storeId = null)
    {
        return $this->isSetFlag(self::ALLOW_ON_TAX, $storeId);
    }

    /**
     * @var int $storeId
     *
     * @return bool
     */
    public function isAllowOnShipping($storeId = null)
    {
        return $this->isSetFlag(self::ALLOW_ON_SHIPPING, $storeId);
    }

    /**
     * @return bool
     */
    public function isEmailEnabled()
    {
        return $this->isSetFlag(self::EMAIL_ENABLED);
    }

    /**
     * @return array
     */
    public function getEmailActions()
    {
        return explode(',', $this->getValue(self::EMAIL_ACTIONS));
    }

    /**
     * @return string
     */
    public function getEmailSender()
    {
        return $this->getValue(self::EMAIL_SENDER);
    }

    /**
     * @return string
     */
    public function getEmailReplyTo()
    {
        return $this->getValue(self::EMAIL_REPLY);
    }

    /**
     * @return string
     */
    public function getEmailTemplate($storeId = 0)
    {
        return $this->getValue(self::EMAIL_TEMPLATE, $storeId);
    }

    public function getConfigValue(...$args)
    {
        return $this->getValue(...$args);
    }

    public function getGlobalConfigValue($path)
    {
        $this->scopeConfig->getValue($this->pathPrefix . $path);
    }
}
