<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Controller\Adminhtml\Import;

use Amasty\ProductAttachment\Controller\Adminhtml\Import;
use Amasty\ProductAttachment\Model\Import\Repository;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class ImportContinue extends Import
{
    /**
     * @var Repository
     */
    private $repository;

    public function __construct(
        Repository $repository,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->repository = $repository;
    }

    public function execute()
    {
        if ($importId = $this->getRequest()->getParam('import_id')) {
            try {
                $import = $this->repository->getById($importId);
                $storeIds = $import->getData(\Amasty\ProductAttachment\Model\Import\Import::STORE_IDS);
                if ($storeIds === null || $storeIds === '') {
                    $this->_redirect(
                        $this->getUrl(
                            'amfile/import/store',
                            ['import_id' => $importId]
                        )
                    );
                    return;
                }
                $this->_redirect(
                    $this->getUrl(
                        'amfile/import/fileImport',
                        ['import_id' => $importId]
                    )
                );
                return;
            } catch (\Exception $e) {

            }
        }
        $this->_redirect('/*/*');
    }
}
