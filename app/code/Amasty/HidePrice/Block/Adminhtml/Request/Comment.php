<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Block\Adminhtml\Request;

use Magento\Backend\Block\Template;
use Amasty\HidePrice\Model\Source\ReplaceButton;

class Comment extends Template
{
    /**
     * @var \Amasty\HidePrice\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(
        \Amasty\HidePrice\Helper\Data $helper,
        \Magento\Framework\Module\Manager $moduleManager,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return bool
     */
    public function isNeedShow()
    {
        return $this->moduleManager->isEnabled('Amasty_Customform')
            && $this->helper->getModuleConfig('information/replace_with') == ReplaceButton::CUSTOM_FORM;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->_urlBuilder->getUrl('amasty_customform/answer/index', [
            'form_id' => $this->helper->getModuleConfig('information/custom_form')
        ]);
    }
}
