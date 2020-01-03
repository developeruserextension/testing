<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Block\Adminhtml;

use Magento\Backend\Block\Template;

class Steps extends Template
{
    const STEP1 = 'files';
    const STEP2 = 'stores';
    const STEP3 = 'import';

    const STEPS = [
        self::STEP1 => 'Prepare Files For Import',
        self::STEP2 => 'Select Stores For Configuration',
        self::STEP3 => 'Import Your Files'
    ];

    /**
     * @var string
     */
    private $currentStep;

    /**
     * @var string
     */
    private $backLink;

    /**
     * @var string
     */
    private $nextLink;

    public function setCurrentStep($currentStep)
    {
        $this->currentStep = $currentStep;

        return $this;
    }

    public function getCurrentStep()
    {
        if (!array_key_exists($this->currentStep, self::STEPS)) {
            return self::STEP1;
        }

        return $this->currentStep;
    }

    public function getSteps()
    {
        return self::STEPS;
    }

    public function getBackLink()
    {
        return $this->backLink;
    }

    public function setBackLink($backLink)
    {
        $this->backLink= $backLink;

        return $this;
    }

    public function getNextLink()
    {
        return $this->nextLink;
    }

    public function setNextLink($nextLink)
    {
        $this->nextLink = $nextLink;

        return $this;
    }

    public function getImportListingUrl()
    {
        return $this->getUrl('amfile/import/index');
    }
}
