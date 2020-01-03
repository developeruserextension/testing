<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Model\Config\Backend\Popup;

use Magento\Framework\Exception\LocalizedException;
use Amasty\HidePrice\Model\Source\ReplaceButton;

class Customform extends \Magento\Framework\App\Config\Value
{
    /**
     * @return $this
     * @throws LocalizedException
     */
    public function save()
    {
        if ($this->getValue() == ReplaceButton::CUSTOM_FORM
            && $this->getModuleManager()
            && $this->getModuleManager()->isEnabled('Amasty_Customform') === false
        ) {
            throw new LocalizedException(
                __('Custom Forms module was not installed.')
            );
        }

        return parent::save();
    }
}
