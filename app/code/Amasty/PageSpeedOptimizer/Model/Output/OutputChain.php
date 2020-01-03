<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_PageSpeedOptimizer
 */


namespace Amasty\PageSpeedOptimizer\Model\Output;

class OutputChain implements OutputChainInterface
{
    /**
     * @var OutputProcessorInterface[]
     */
    private $processors;

    public function __construct(
        $processors
    ) {
        $this->processors = $processors;
    }

    /**
     * @inheritdoc
     */
    public function process(&$output, $deviceDetect)
    {
        $result = true;
        foreach ($this->processors as $processor) {
            if (!$processor->process($output, $deviceDetect)) {
                $result = false;
                break;
            }
        }

        return $result;
    }
}
