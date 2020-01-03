<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\File\DataProvider;

use Amasty\ProductAttachment\Api\Data\FileInterface;
use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\File\FileScope\FileScopeDataProviderInterface;
use Amasty\ProductAttachment\Model\File\Repository;
use Amasty\ProductAttachment\Model\File\ResourceModel\CollectionFactory;
use Amasty\ProductAttachment\Model\Filesystem\UrlResolver;
use Amasty\ProductAttachment\Model\Icon\GetIconForFile;
use Amasty\ProductAttachment\Model\Icon\ResourceModel\Icon;
use Amasty\ProductAttachment\Model\SourceOptions\AttachmentType;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class DataProvider
 */
class Form extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var UrlResolver
     */
    private $urlResolver;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var GetIconForFile
     */
    private $getIconForFile;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var \Magento\Framework\File\Size
     */
    private $fileSize;

    /**
     * @var Icon
     */
    private $iconResourceModel;

    /**
     * @var FormProductDetails
     */
    private $formProductDetails;

    /**
     * @var Modifiers\Category
     */
    private $categoryModifier;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $url;

    /**
     * @var FileInterface
     */
    private $file;

    /**
     * @var FileScopeDataProviderInterface
     */
    private $fileScopeDataProvider;

    public function __construct(
        CollectionFactory $fileCollectionFactory,
        Repository $repository,
        UrlResolver $urlResolver,
        RequestInterface $request,
        GetIconForFile $getIconForFile,
        DataPersistorInterface $dataPersistor,
        Icon $iconResourceModel,
        FormProductDetails $formProductDetails,
        Modifiers\Category $categoryModifier,
        \Magento\Framework\File\Size $fileSize,
        \Magento\Backend\Model\UrlInterface $url,
        FileScopeDataProviderInterface $fileScopeDataProvider,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $fileCollectionFactory->create();
        $this->urlResolver = $urlResolver;
        $this->request = $request;
        $this->getIconForFile = $getIconForFile;
        $this->repository = $repository;
        $this->fileSize = $fileSize;
        $this->iconResourceModel = $iconResourceModel;
        $this->formProductDetails = $formProductDetails;
        $this->categoryModifier = $categoryModifier;
        $this->dataPersistor = $dataPersistor;
        $this->url = $url;
        $this->fileScopeDataProvider = $fileScopeDataProvider;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        if ($data['totalRecords'] > 0) {
            $fileData = $this->file->getData();
            if ($this->file->getAttachmentType() == AttachmentType::FILE && $this->file->getFilePath()) {
                $fileData['file'] = [
                    [
                        'name' => $fileData[FileInterface::FILENAME],
                        'url'  => $this->urlResolver->getAttachmentUrlByName($this->file->getFilePath()),
                        'previewUrl'  => $this->getIconForFile->byFileExtension($this->file->getFileExtension()),
                        'previewType' => 'image',
                        'size' => $this->file->getFileSize()
                    ]
                ];
            }

            if (!empty($fileData[FileInterface::PRODUCTS])) {
                $this->formProductDetails->addProductDetails($fileData);
            }

            $data[$this->file->getFileId()] = $fileData;
        }

        if ($savedData = $this->dataPersistor->get(RegistryConstants::FILE_DATA)) {
            $savedFileId = isset($savedData[FileInterface::FILE_ID]) ? $savedData[FileInterface::FILE_ID] : null;
            if (isset($data[$savedFileId])) {
                $data[$savedFileId] = array_merge($data[$savedFileId], $savedData);
            } else {
                $data[$savedFileId] = $savedData;
            }
            $this->dataPersistor->clear(RegistryConstants::FILE_DATA);
        }

        return $data;
    }

    public function getMeta()
    {
        $meta = parent::getMeta();
        $this->data['config']['submit_url'] = $this->url->getUrl('*/*/save', ['_current' => true]);
        $meta['general']['children']['file']['arguments']['data']['config']['maxFileSize'] =
            $this->fileSize->getMaxFileSize();
        $meta['general']['children']['file']['arguments']['data']['config']['allowedExtensions'] =
            $this->iconResourceModel->getAllowedExtensions();

        $fileId = (int)$this->request->getParam(RegistryConstants::FORM_FILE_ID);
        $store = (int)$this->request->getParam('store');
        if ($fileId) {
            try {
                $this->file = $this->repository->getById($fileId);
                $this->file = $this->fileScopeDataProvider->execute(
                    [
                        RegistryConstants::FILE => $this->file,
                        RegistryConstants::STORE => $store
                    ],
                    'file'
                );
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                null;
            }
        }

        if ($store === 0) {
            $meta['additional']['children']['use_default_categories']
            ['arguments']['data']['config']['componentDisabled'] = true;
            $meta['additional']['children']['use_default_products']
            ['arguments']['data']['config']['componentDisabled'] = true;
        } else {
            if (!$this->file->getData(RegistryConstants::USE_DEFAULT_PREFIX . FileInterface::CATEGORIES)) {
                $meta['additional']['children']['use_default_categories']
                ['arguments']['data']['config']['default'] = 0;
            }

            if (!$this->file->getData(RegistryConstants::USE_DEFAULT_PREFIX . FileInterface::PRODUCTS)) {
                $meta['additional']['children']['use_default_products']
                ['arguments']['data']['config']['default'] = 0;
            }
        }

        $this->categoryModifier->addCategoryField($meta, $store);
        if ($this->file && $store) {
            $config = [
                'scopeLabel' => __('[STORE VIEW]')
            ];

            $config['service'] = [
                'template' => 'ui/form/element/helper/service',
            ];

            foreach (array_merge(RegistryConstants::USE_DEFAULT_FIELDS, [FileInterface::CUSTOMER_GROUPS]) as $field) {
                $config['disabled'] = (bool)$this->file->getData(RegistryConstants::USE_DEFAULT_PREFIX . $field);
                $meta['general']['children'][$field]['arguments']['data']['config'] = $config;
            }
        }

        return $meta;
    }
}
