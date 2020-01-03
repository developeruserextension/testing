<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_PageSpeedOptimizer
 */


namespace Amasty\PageSpeedOptimizer\Plugin;

use Amasty\PageSpeedOptimizer\Model\ConfigProvider;
use Amasty\PageSpeedOptimizer\Model\Output\DeviceDetect;
use Amasty\PageSpeedOptimizer\Model\Output\OutputChainInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class ProcessPageResult
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var OutputChainInterface
     */
    private $outputChain;

    /**
     * @var DeviceDetect
     */
    private $deviceDetect;

    public function __construct(
        ConfigProvider $configProvider,
        OutputChainInterface $outputChain,
        DeviceDetect $deviceDetect
    ) {
        $this->configProvider = $configProvider;
        $this->outputChain = $outputChain;
        $this->deviceDetect = $deviceDetect;
    }

    public function aroundRenderResult(ResultInterface $subject, \Closure $proceed, ResponseInterface $response)
    {
        /** @var ResultInterface $result */
        $result = $proceed($response);

        if (!$this->configProvider->isEnabled()) {
            return $result;
        }

        $output = $response->getBody();
        if ($this->outputChain->process($output, $this->deviceDetect)) {
            $response->setBody($output);
        }

        return $result;
    }
}
