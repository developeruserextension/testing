<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_PageSpeedOptimizer
 */


namespace Amasty\PageSpeedOptimizer\Model\Image;

use Amasty\PageSpeedOptimizer\Exceptions\DisabledExecFunction;
use Amasty\PageSpeedOptimizer\Exceptions\ToolNotInstalled;

class ToolChecker
{
    public function check($command)
    {
        $disabled = explode(',', str_replace(' ', ',', ini_get('disable_functions')));
        if (in_array('exec', $disabled)) {
            throw new DisabledExecFunction();
        }

        if (empty($command['check']) || empty($command['check']['command']) || empty($command['check']['result'])) {
            return;
        }

        $output = [];
        /** @codingStandardsIgnoreStart */
        exec($command['check']['command'] . ' 2>&1', $output);
        /** @codingStandardsIgnoreEnd */
        if (!empty($output)) {
            foreach ($output as $line) {
                if (false !== strpos($line, $command['check']['result'])) {
                    return;
                }
            }
        }

        throw new ToolNotInstalled(__('Image Optimization Tool "%1" is not installed', $command['name']));
    }
}
