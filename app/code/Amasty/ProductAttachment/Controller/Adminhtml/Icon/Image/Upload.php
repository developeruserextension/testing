<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Controller\Adminhtml\Icon\Image;

use Amasty\ProductAttachment\Api\Data\IconInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Upload
 */
class Upload extends \Amasty\ProductAttachment\Controller\Adminhtml\Icon
{
    /**
     * @var \Amasty\ProductAttachment\Model\Filesystem\FileUploader
     */
    private $fileUploader;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Amasty\ProductAttachment\Model\Filesystem\FileUploader $fileUploader
    ) {
        parent::__construct($context);
        $this->fileUploader = $fileUploader;
    }

    /**
     * Upload Icon controller action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        return $resultJson->setData($this->fileUploader->uploadFile(RegistryConstants::ICON_FILE_KEY));
    }
}
