<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Import;

use Amasty\Base\Model\Import\Behavior\BehaviorProviderInterface;
use Amasty\Base\Model\Import\Mapping\MappingInterface;
use Amasty\Base\Model\Import\Validation\EncodingValidator;
use Amasty\Base\Model\Import\Validation\ValidatorPoolInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ImportFactory;
use Magento\ImportExport\Model\ResourceModel\Helper;

class ImportProcess extends \Amasty\Base\Model\Import\AbstractImport
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Repository
     */
    private $repository;

    public function __construct(
        Registry $registry,
        Repository $repository,
        $entityTypeCode,
        ValidatorPoolInterface $validatorPool,
        BehaviorProviderInterface $behaviorProvider,
        MappingInterface $mapping,
        EncodingValidator $encodingValidator,
        StringUtils $string,
        ScopeConfigInterface $scopeConfig,
        ImportFactory $importFactory,
        Helper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        ResourceConnection $resource,
        array $data = []
    ) {
        parent::__construct(
            $entityTypeCode,
            $validatorPool,
            $behaviorProvider,
            $mapping,
            $encodingValidator,
            $string,
            $scopeConfig,
            $importFactory,
            $resourceHelper,
            $errorAggregator,
            $resource,
            $data
        );
        $this->registry = $registry;
        $this->repository = $repository;
    }

    public function processImport()
    {
        parent::processImport();
        if ($importId = $this->registry->registry('amfile_import_id')) {
            $this->repository->deleteById($importId);
        }
    }
}
