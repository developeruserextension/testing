<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Checkout
 */


namespace Amasty\Checkout\Model;

use Magento\Framework\DataObject;
use Amasty\Checkout\Api\Data\TotalsInterface;

/**
 * Class Totals
 */
class Totals extends DataObject implements TotalsInterface
{
    /**
     * @inheritdoc
     */
    public function getTotals()
    {
        return $this->getData(self::TOTALS);
    }

    /**
     * @inheritdoc
     */
    public function getImageData()
    {
        return $this->getData(self::IMAGE_DATA);
    }

    /**
     * @inheritdoc
     */
    public function getOptionsData()
    {
        return $this->getData(self::OPTIONS_DATA);
    }

    /**
     * @inheritdoc
     */
    public function getShipping()
    {
        return $this->getData(self::SHIPPING);
    }

    /**
     * @inheritdoc
     */
    public function getPayment()
    {
        return $this->getData(self::PAYMENT);
    }
}
