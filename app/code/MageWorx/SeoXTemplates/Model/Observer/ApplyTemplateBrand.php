<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Observer;

use MageWorx\SeoXTemplates\Model\ResourceModel\Template\Brand\CollectionFactory;
use MageWorx\SeoXTemplates\Helper\Store as HelperStore;
use Magento\Framework\Stdlib\DateTime\DateTime;
use MageWorx\SeoXTemplates\Model\DbWriterBrandFactory;
use MageWorx\SeoXTemplates\Helper\Data as HelperData;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Observer class for brand page template apply proccess
 */
class ApplyTemplateBrand implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var DbWriterBrandFactory
     */
    protected $dbWriterBrandFactory;

    /**
     *
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperStore
     */
    protected $helperStore;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *
     * @var \MageWorx\SeoXTemplates\Model\ResourceModel\Template\Brand\CollectionFactory
     */
    protected $templateBrandCollectionFactory;

    /**
     *
     * @param DateTime $date
     * @param DbWriterBrandFactory $dbWriterBrandFactory
     * @param HelperData $helperData
     * @param CollectionFactory $templateBrandCollectionFactory
     * @param HelperStore $helperStore
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        DateTime $date,
        DbWriterBrandFactory $dbWriterBrandFactory,
        HelperData $helperData,
        CollectionFactory $templateBrandCollectionFactory,
        HelperStore $helperStore,
        StoreManagerInterface $storeManager
    ) {
        $this->date = $date;
        $this->dbWriterBrandFactory = $dbWriterBrandFactory;
        $this->helperData = $helperData;
        $this->templateBrandCollectionFactory = $templateBrandCollectionFactory;
        $this->helperStore = $helperStore;
        $this->storeManager = $storeManager;
    }

    /**
     * Apply brand page template
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $this->templateBrandCollectionFactory->create();

        if ($observer->getData('templateIds')) {
            $collection->addStoreModeFilter($this->storeManager->isSingleStoreMode());
            $collection->loadByIds($observer->getData('templateIds'));
        } elseif ($observer->getData('templateTypeId')) {
            $collection->addStoreModeFilter($this->storeManager->isSingleStoreMode());
            $collection->addTypeFilter($observer->getData('templateTypeId'));
            $collection->addCronFilter();
        }

        foreach ($collection as $template) {

            $template->setDateApplyStart($this->date->gmtDate());
            $template->loadItems();

            if ($template->getStoreId() == 0
                && !$template->getIsSingleStoreMode()
                && !$template->getUseForDefaultValue()
            ) {
                $storeIds = array_keys($this->helperStore->getActiveStores());

                foreach ($storeIds as $storeId) {
                    $this->writeTemplateForStore($template, $storeId);
                }
            } else {
                $this->writeTemplateForStore($template);
            }

            $template->setDateApplyFinish($this->date->gmtDate());
            $template->save();
        }
    }

    /**
     * @param \MageWorx\SeoXTemplates\Model\Template\Brand $template
     * @param null $nestedStoreId
     */
    protected function writeTemplateForStore($template, $nestedStoreId = null)
    {
        $from      = 0;
        $limit     = $this->helperData->getTemplateLimitForCurrentStore();
        $dbWriter  = $this->dbWriterBrandFactory->create($template->getTypeId());

        $collection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);

        while (is_object($collection) && $collection->count() > 0) {
            $dbWriter->write($collection, $template, $nestedStoreId);
            $from += $limit;
            $collection = $template->getItemCollectionForApply($from, $limit, null, $nestedStoreId);
        }
    }
}
