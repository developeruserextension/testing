<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachmentApi
 */


namespace Amasty\ProductAttachmentApi\Model;

use Amasty\ProductAttachmentApi\Api\UploadFileInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\Filesystem\FileUploader;
use Magento\Framework\Exception\LocalizedException;

class UploadFile implements UploadFileInterface
{
    /**
     * @var FileUploader
     */
    private $fileUploader;

    public function __construct(
        FileUploader $fileUploader
    ) {
        $this->fileUploader = $fileUploader;
    }

    /**
     * @inheritdoc
     */
    public function upload(\Amasty\ProductAttachmentApi\Api\Data\FileContentInterface $fileContent)
    {
        if (!($file = base64_decode($fileContent->getBase64EncodedData()))) {
            throw new LocalizedException(__('Base64Decode File Error'));
        }
        /** @codingStandardsIgnoreStart */
        $temp = tmpfile();
        fwrite($temp, $file);
        fseek($temp, 0);

        $_FILES[RegistryConstants::FILE_KEY] = [
            'name' => $fileContent->getNameWithExtension(),
            'type' => mime_content_type(stream_get_meta_data($temp)['uri']),
            'tmp_name' => stream_get_meta_data($temp)['uri'],
            'error' => UPLOAD_ERR_OK,
            'size' => count($file)
        ];

        $uploadedFile = $this->fileUploader->uploadFile(RegistryConstants::FILE_KEY);
        if (!empty($uploadedFile['error'])) {
            throw new LocalizedException(__($uploadedFile['error']));
        }
        $fileContent->setBase64EncodedData(null);
        $fileContent->setNameWithExtension(null);
        $fileContent->setTemporaryName($uploadedFile['name']);
        /** @codingStandardsIgnoreEnd */

        return $fileContent;
    }
}
