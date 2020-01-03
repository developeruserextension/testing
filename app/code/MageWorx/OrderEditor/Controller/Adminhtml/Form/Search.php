<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Controller\Adminhtml\Form;

use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObject;
use Magento\Sales\Controller\Adminhtml\Order\Create;

/**
 * Class Search
 */
class Search extends Create
{
    /**
     * @return JsonResult
     */
    public function execute()
    {
        $updateResult = new DataObject();
        try {
            $resultPage = $this->resultPageFactory->create();
            $html = '';

            $optionsBlock = $resultPage->getLayout()->getBlock('search');
            if (!empty($optionsBlock)) {
                $html .= $optionsBlock->toHtml();
            }

            $createBlock = $resultPage->getLayout()->getBlock('create');
            if (!empty($createBlock)) {
                $html .= $createBlock->toHtml();
            }

            $updateResult->setSearchGrid($html);
            $updateResult->setOk(true);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $updateResult->setError(true);
            $updateResult->setMessage($errorMessage);
        }

        /** @var JsonResult $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        return $resultJson->setData($updateResult);
    }
}
