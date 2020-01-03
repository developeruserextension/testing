<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Controller\Adminhtml\File;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\File;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Magento\Backend\App\Action\Context;
use Amasty\ProductAttachment\Model\File\FileFactory;
use Amasty\ProductAttachment\Api\FileRepositoryInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends File
{
    /**
     * @var FileRepositoryInterface
     */
    private $repository;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        FileRepositoryInterface $repository,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->repository = $repository;
        $this->dataPersistor = $dataPersistor;
    }

    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                /** @var \Amasty\ProductAttachment\Model\File\File $model */
                $model = $this->fileFactory->create();
                $data = $this->getRequest()->getPostValue();

                if ($fileId = (int)$this->getRequest()->getParam(RegistryConstants::FORM_FILE_ID)) {
                    $model = $this->repository->getById($fileId);
                    if ($fileId != $model->getFileId()) {
                        throw new LocalizedException(__('The wrong item is specified.'));
                    }
                }

                $this->filterData($data);
                $model->addData($data);
                $this->repository->saveAll($model);

                $this->messageManager->addSuccessMessage(__('You saved the item.'));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect(
                        '*/*/edit',
                        [RegistryConstants::FORM_FILE_ID => $model->getId(), '_current' => true]
                    );
                    return;
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->dataPersistor->set(RegistryConstants::FILE_DATA, $data);
                if ($fileId = (int)$this->getRequest()->getParam(RegistryConstants::FORM_FILE_ID)) {
                    $this->_redirect('*/*/edit', [RegistryConstants::FORM_FILE_ID => $fileId]);
                } else {
                    $this->_redirect('*/*/create');
                }
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * @param array $data
     */
    private function filterData(&$data)
    {
        if (!empty($data['fileproducts']['products'])) {
            $productIds = [];
            foreach ($data['fileproducts']['products'] as $product) {
                $productIds[] = (int)$product['entity_id'];
            }
            $data[FileInterface::PRODUCTS] = array_unique($productIds);
        } else {
            $data[FileInterface::PRODUCTS] = [];
        }
    }
}
