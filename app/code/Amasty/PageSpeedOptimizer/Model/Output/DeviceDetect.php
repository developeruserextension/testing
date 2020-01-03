<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_PageSpeedOptimizer
 */


namespace Amasty\PageSpeedOptimizer\Model\Output;

use Amasty\PageSpeedOptimizer\Lib\MobileDetect;

class DeviceDetect extends MobileDetect
{
    /**
     * @var string
     */
    private $webPBrowsersString = '/(Edge|Firefox|Chrome|Opera)/i';

    /**
     * @inheritdoc
     */
    public function getDeviceType()
    {
        if ($this->isTablet()) {
            return 'tablet';
        }
        if ($this->isMobile()) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * @inheritdoc
     */
    public function isUseWebP()
    {
        $userAgent = $this->getUserAgent();

        if (preg_match($this->webPBrowsersString, $userAgent)) {
            return true;
        }

        return false;
    }
}
