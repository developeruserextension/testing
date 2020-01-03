<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_PageSpeedOptimizer
 */


namespace Amasty\PageSpeedOptimizer\Model\Output;

use Amasty\PageSpeedOptimizer\Model\Output\DeviceDetect;

interface OutputProcessorInterface
{
    /**
     * @param string &$output
     * @param DeviceDetect
     *
     * @return string
     */
    public function process(&$output, $deviceDetect);
}
