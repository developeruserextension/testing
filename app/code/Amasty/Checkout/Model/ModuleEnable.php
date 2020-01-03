<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Checkout
 */


namespace Amasty\Checkout\Model;

/**
 * Class ModuleEnable
 */
class ModuleEnable
{
    const TIG_POSTNL_MODULE_NAMESPACE = 'TIG_PostNL';
    const AMASTY_STOCKSTATUS_MODULE_NAMESPACE = 'Amasty_Stockstatus';
    const MODULE_ORDER_ATTRIBUTES = 'Amasty_Orderattr';
    const MODULE_CUSTOMER_ATTRIBUTES = 'Amasty_CustomerAttributes';

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(\Magento\Framework\Module\Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return bool
     */
    public function isPostNlEnable()
    {
        return $this->moduleManager->isEnabled(self::TIG_POSTNL_MODULE_NAMESPACE);
    }

    /**
     * @return bool
     */
    public function isStockStatusEnable()
    {
        return $this->moduleManager->isEnabled(self::AMASTY_STOCKSTATUS_MODULE_NAMESPACE);
    }

    /**
     * @return bool
     */
    public function isOrderAttributesEnable()
    {
        return $this->moduleManager->isEnabled(self::MODULE_ORDER_ATTRIBUTES);
    }

    /**
     * @return bool
     */
    public function isCustomerAttributesEnable()
    {
        return $this->moduleManager->isEnabled(self::MODULE_CUSTOMER_ATTRIBUTES);
    }
}
