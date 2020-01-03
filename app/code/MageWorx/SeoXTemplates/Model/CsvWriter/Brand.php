<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Model\CsvWriter;

use Magento\Framework\Filesystem;
use MageWorx\SeoXTemplates\Model\DataProviderBrandFactory;

class Brand extends \MageWorx\SeoXTemplates\Model\CsvWriter
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var DataProviderBrandFactory
     */
    protected $dataProviderBrandFactory;

    /**
     * Brand constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $_storeManager
     * @param Filesystem $fileSystem
     * @param DataProviderBrandFactory $dataProviderBrandFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $_storeManager,
        Filesystem $fileSystem,
        DataProviderBrandFactory $dataProviderBrandFactory
    ) {
        $this->_storeManager = $_storeManager;
        $this->dataProviderBrandFactory = $dataProviderBrandFactory;
        parent::__construct($fileSystem);
    }

    /**
     * Write to CSV file converted string from template code and retrive file params
     *
     * @param \MageWorx\SeoXTemplates\Model\ResourceModel\Template\Brand\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param string|null $filenameParam
     * @param int|null $nestedStoreId
     * @return array
     */
    public function write($collection, $template, $filenameParam = null, $nestedStoreId = null)
    {
        if (!$collection->count()) {
            return false;
        }

        $dataProvider = $this->dataProviderBrandFactory->create($template->getTypeId());

        $data = $dataProvider->getData($collection, $template, $nestedStoreId);
        $filename = $filenameParam ? $filenameParam : 'export/' . hash('md5', microtime()) . '.csv';

        $stream = $this->directory->openFile($filename, 'a+');
        if (!$filenameParam) {
            $stream->writeCsv($this->_getHeaderData());
        }

        $stream->lock();

        foreach ($data as $brandId => $brandData) {
            if (empty($brandData['value'])) {
                continue;
            }

            $write = $brandData;
            $stream->writeCsv($write);
        }

        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $filename,
            'rm' => true  // can delete file after use
        ];
    }

    /**
     * Retrieve header for report
     *
     * @return array
     */
    protected function _getHeaderData()
    {
        return [
            __('Brand ID'),
            __('Brand Title'),
            __('Store ID'),
            __('Store Name'),
            __('Target'),
            __('Current Store Value'),
            __('New Store Value')
        ];
    }
}
