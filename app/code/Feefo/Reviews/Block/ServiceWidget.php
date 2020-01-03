<?php

namespace Feefo\Reviews\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class ServiceWidget
 */
class ServiceWidget extends AbstractWidget
{
    /**
     * @var string
     */
    protected $_template = 'Feefo_Reviews::service.phtml';

    /**
     * Retrieve a code snippet for the current widget
     *
     * @return string
     */
    public function getSnippet()
    {
        return $this->getWidgetSnippets()->getServiceSnippet();
    }
}