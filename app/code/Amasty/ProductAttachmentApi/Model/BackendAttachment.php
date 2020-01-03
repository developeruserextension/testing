<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachmentApi
 */


namespace Amasty\ProductAttachmentApi\Model;

use Amasty\ProductAttachment\Model\Filesystem\Directory;
use Amasty\ProductAttachmentApi\Api\BackendAttachmentInterface;
use Amasty\ProductAttachmentApi\Api\Data\BackendFileInterface;
use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Api\FileRepositoryInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\File\FileScope\FileScopeDataProviderInterface;
use Amasty\ProductAttachment\Model\File\ResourceModel\CollectionFactory;
use Amasty\ProductAttachment\Model\File\ResourceModel\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;

class BackendAttachment implements BackendAttachmentInterface
{
    /**
     * @var FileRepositoryInterface
     */
    private $fileRepository;

    /**
     * @var FileScopeDataProviderInterface
     */
    private $fileScopeDataProvider;

    /**
     * @var File\BackendFileFactory
     */
    private $backendFileFactory;

    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CollectionFactory
     */
    private $fileCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        FileRepositoryInterface $fileRepository,
        FileScopeDataProviderInterface $fileScopeDataProvider,
        File\BackendFileFactory $backendFileFactory,
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        StoreManagerInterface $storeManager,
        CollectionFactory $fileCollectionFactory
    ) {
        $this->fileRepository = $fileRepository;
        $this->fileScopeDataProvider = $fileScopeDataProvider;
        $this->backendFileFactory = $backendFileFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->fileCollectionFactory = $fileCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function save(\Amasty\ProductAttachmentApi\Api\Data\BackendFileInterface $file)
    {
        $storeId = $this->storeManager->getStore()->getId();
        if ($tmpFile = $file->getTmpFile()) {
            $file->setData(
                RegistryConstants::FILE_KEY,
                [
                    [
                        'name' => $tmpFile,
                        'tmp_name' => $tmpFile,
                        'file' => $tmpFile
                    ]
                ]
            );
        }

        if (!$storeId || !$file->getFileId()) {
            if (empty($file->getLabel())) {
                throw new LocalizedException(__('Label is required field.'));
            }

            if (empty($file->getFileName())) {
                throw new LocalizedException(__('Filename is required field.'));
            }
        }

        if ($file->getAttachmentCategories() === null) {
            $file->setData('use_default_categories', true);
        } elseif ($file->getAttachmentCategories() === false || ($file->getAttachmentCategories() === [])) {
            $file->setData('use_default_categories', false);
            $file->setData(FileInterface::CATEGORIES, []);
        } elseif ($categories = $file->getAttachmentCategories()) {
            $file->setData('use_default_categories', false);
            $file->setData(FileInterface::CATEGORIES, $categories);
        }

        if ($file->getAttachmentProducts() === null) {
            $file->setData('use_default_products', true);
        } elseif ($file->getAttachmentProducts() === false || ($file->getAttachmentProducts() === [])) {
            $file->setData('use_default_products', false);
            $file->setData(FileInterface::PRODUCTS, []);
        } elseif ($products = $file->getAttachmentProducts()) {
            $file->setData('use_default_products', false);
            $file->setData(FileInterface::PRODUCTS, $products);
        }

        if ($this->storeManager->getStore()->getId() && $file->getCustomerGroups() === null) {
            $file->setData(BackendFileInterface::CUSTOMER_GROUPS . '_output', null);
        } elseif (is_array($file->getCustomerGroups())) {
            $file->setData(
                BackendFileInterface::CUSTOMER_GROUPS . '_output',
                implode(',', $file->getCustomerGroups())
            );
        }

        $this->fileRepository->saveAll(
            $file,
            [RegistryConstants::STORE => (int)$this->storeManager->getStore()->getId()]
        );

        return $this->getById($file->getFileId(), (int)$this->storeManager->getStore()->getId());
    }

    /**
     * @inheritdoc
     */
    public function getById($fileId)
    {
        $file = $this->fileScopeDataProvider->execute(
            [
                RegistryConstants::FILE => $this->fileRepository->getById($fileId),
                RegistryConstants::STORE => $this->storeManager->getStore()->getId()
            ],
            'file'
        );

        /** @var BackendFileInterface $apiFile */
        $apiFile = $this->backendFileFactory->create();
        $apiFile->setData($file->getData());

        if ($categories = $file->getData(FileInterface::CATEGORIES)) {
            $apiFile->setAttachmentCategories($categories);
        }

        if ($products = $file->getData(FileInterface::PRODUCTS)) {
            $apiFile->setAttachmentProducts($products);
        }

        $apiFile->setFrontendUrl(
            $this->storeManager->getStore()->getBaseUrl()
                . 'pub' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR
                . Directory::DIRECTORY_CODES[Directory::ATTACHMENT]
                . DIRECTORY_SEPARATOR . $apiFile->getFilePath()
        );

        return $apiFile;
    }

    /**
     * @inheritdoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\ProductAttachment\Model\File\ResourceModel\Collection $filesCollection */
        $filesCollection = $this->fileCollectionFactory->create();
        $filesCollection->addFileData($this->storeManager->getStore()->getId());
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $filesCollection);
        }
        $searchResults->setTotalCount($filesCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $filesCollection);
        }
        $filesCollection->setCurPage($searchCriteria->getCurrentPage());
        $filesCollection->setPageSize($searchCriteria->getPageSize());
        $files = [];
        /** @var \Amasty\ProductAttachmentApi\Api\Data\BackendFileInterface $file */
        foreach ($filesCollection->getData() as $file) {
            $files[] = $this->getById($file[BackendFileInterface::FILE_ID])->getData();
        }
        $searchResults->setItems($files);
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($fileId)
    {
        return $this->fileRepository->deleteById($fileId);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $filesCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $filesCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $filesCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection  $filesCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $filesCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $filesCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? 'DESC' : 'ASC'
            );
        }
    }
}
