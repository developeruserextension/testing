<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Template;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Store\Model\Store;
use MageWorx\SeoXTemplates\Model\Template\Brand\Source\AttributeCode as AttributeCodeOptions;
use MageWorx\SeoXTemplates\Model\Template\Source\AssignType as AssignTypeOptions;
use MageWorx\SeoXTemplates\Model\Template\Source\Scope as ScopeOptions;
use MageWorx\SeoXTemplates\Model\Template\Source\IsUseCron as IsUseCronOptions;
use MageWorx\SeoXTemplates\Model\Template\Brand\Source\Type as TypeOptions;
use MageWorx\SeoXTemplates\Helper\Store as HelperStore;
use Magento\Framework\Message\ManagerInterface as MagentoManagerInterface;
use Magento\Framework\DataObject;

/**
 * @method int getTypeId()
 * @method Template setTypeId(\int $title)
 * @method string getName()
 * @method Template setName(\string $name)
 * @method int getStoreId()
 * @method Template setStoreId(\int $storeId)
 * @method string getCode()
 * @method Template setCode(\string $code)
 * @method int getAssignType()
 * @method Template setAssignType(\int $assignType)
 * @method int getPriority()
 * @method Template setPriority(\int $priority)
 * @method string getDateModified()
 * @method Template setDateModified(\string $dateModified)
 * @method string getDateApplyStart()
 * @method Template setDateApplyStart(\string $dateApplyStart)
 * @method string getDateApplyFinish()
 * @method Template setDateApplyFinish(\string $dateApplyFinish)
 * @method int geScope()
 * @method Template setScope(\int $scope)
 * @method bool getUseCron()
 * @method Template setIsUseCrone(\bool $isUseCron)
 * @method bool getIsUseCrone()
 * @method int getIsSingleStoreMode()
 * @method Template setIsSingleStoreMode(\int $isSingleStoreMode)
 */
class Brand extends \MageWorx\SeoXTemplates\Model\AbstractTemplate
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageworx_seoxtemplates_template_brand';

    /**
     * Type value for brand page title
     *
     * @var int
     */
    const TYPE_BRAND_PAGE_TITLE = 1;

    /**
     * Type value for brand meta title
     *
     * @var int
     */
    const TYPE_BRAND_META_TITLE = 2;

    /**
     * Type value for brand meta description
     *
     * @var int
     */
    const TYPE_BRAND_META_DESCRIPTION = 3;

    /**
     * Type value for brand meta keywords
     *
     * @var int
     */
    const TYPE_BRAND_META_KEYWORDS = 4;

    /**
     * Cache tag
     *
     * @var string
     */
    protected $cacheTag = 'mageworx_seoxtemplates_template_brand';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_seoxtemplates_template_brand';

    /**
     * @var HelperStore
     */
    protected $helperStore;

    /**
     * @var DataObject
     */
    protected $brands;

    /**
     *
     * @param Context $context
     * @param Registry $registry
     * @param AttributeCodeOptions $attributeCodeOptions
     * @param AssignTypeOptions $assignTypeOptions
     * @param ScopeOptions $scopeOptions
     * @param IsUseCronOptions $isUseCronOptions
     * @param TypeOptions $typeOptions
     * @param HelperStore $helperStore
     * @param MagentoManagerInterface $messageManager
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AttributeCodeOptions $attributeCodeOptions,
        AssignTypeOptions $assignTypeOptions,
        ScopeOptions $scopeOptions,
        IsUseCronOptions $isUseCronOptions,
        TypeOptions $typeOptions,
        HelperStore $helperStore,
        MagentoManagerInterface $messageManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        $this->attributeCodeOptions = $attributeCodeOptions;
        $this->assignTypeOptions    = $assignTypeOptions;
        $this->scopeOptions         = $scopeOptions;
        $this->isUseCronOptions     = $isUseCronOptions;
        $this->typeOptions          = $typeOptions;
        $this->helperStore          = $helperStore;

        parent::__construct($context, $registry, $messageManager, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\SeoXTemplates\Model\ResourceModel\Template\Brand');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Retrieve array template type ID => template title
     *
     * @return array
     */
    public function getTypeArray()
    {
        return $this->typeOptions->toArray();
    }

    /**
     * Retrieve type title such as "Brand Meta Title", "Brand Page Title", ... by type ID
     *
     * @param int|null $typeId
     * @return string|null
     */
    public function getTypeTitleByType($typeId = null)
    {
        $typeId    = $typeId ? $typeId : $this->getTypeId();
        $typeArray = $this->typeOptions->toArray();

        return empty($typeArray[$typeId]) ? null : $typeArray[$typeId];
    }

    /**
     * Retrieve attribute list for template type ID
     *
     * @param int|null $typeId
     * @return array|null
     */
    public function getAttributeCodesByType($typeId = null)
    {
        $typeId             = $typeId ? $typeId : $this->getTypeId();
        $attributeCodeArray = $this->attributeCodeOptions->toArray();

        return empty($attributeCodeArray[$typeId]) ? null : $attributeCodeArray[$typeId];
    }

    /**
     * Retrieve default values for create
     *
     * @return array
     */
    public function getDefaultValuesForCreate()
    {
        return [
            'type_id'  => 0,
            'store_id' => Store::DEFAULT_STORE_ID,
        ];
    }

    /**
     * Get default category template values for edit
     *
     * @return array
     */
    public function getDefaultValuesForEdit()
    {
        return [
            'is_use_cron' => self::CRON_DISABLED,
            'scopre'      => self::SCOPE_EMPTY,
            'assign_type' => self::ASSIGN_INDIVIDUAL_ITEMS,
        ];
    }

    /**
     * After save proccess
     *
     * @return $this
     */
       public function afterSave()
       {
           if ($this->isAssignForAllItems() && $this->getDuplicateTemplateAssignedForAll()) {
               $this->messageManager->addError(
                   __('The template cannot be saved. There is another template assigned for all categories.')
               );
           }

           ///save related
           if ($this->getId() && $this->isAssignForAllItems()) {
               $this->_getResource()->clearAllRelations($this);
           } elseif ($this->getId() && $this->isAssignForIndividualItems()) {
               $oldAssignType = $this->getOrigData('assign_type');
               $newAssignType = $this->getData('assign_type');

               if ($oldAssignType == $newAssignType) {
                   $itemIds    = $this->getBrandsData() ? $this->getBrandsData() : [];
                   $oldItemIds = $this->getOrigData('brands_data') ? $this->getOrigData('brands_data') : [];

                   if ($oldItemIds == $itemIds) {
                       return parent::afterSave();
                   }

                   $analogItemIds = $this->getBrandIdsAssignedForAnalogTemplate();
                   if (array_intersect(array_map('intval', $itemIds), array_map('intval', $analogItemIds))) {
                       $this->messageManager->addError(
                           __('The template was saved without assigned products. Please add categories manually.')
                       );

                       return parent::afterSave();
                   }
               }

               $this->_getResource()->saveBrandRelation($this);
               $this->messageManager->addSuccess(__('Template "%1" was successfully saved', $this->getName()));
           }

           return parent::afterSave();
       }

    /**
     * Set related items
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadItems()
    {
        if ($this->isAssignForIndividualItems()) {
            $itemIds = $this->_getResource()->getIndividualItemIds($this->getId());
            $this->setBrandsData($itemIds);
        }
    }

    /**
     * Retrieve filtered collection for apply (or count)
     *
     * @param int $from
     * @param int $limit
     * @param bool $onlyCountFlag
     * @param int|null $nestedStoreId
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItemCollectionForApply($from, $limit, $onlyCountFlag = false, $nestedStoreId = null)
    {
        $microtime = microtime(1);

        $storeId = $this->getIsSingleStoreMode() ? $this->helperStore->getCurrentStoreId() : $this->getStoreId();

        if ($storeId === '0' && !$this->getUseForDefaultValue()) {
            if ($this->issetUniqStoreTemplateForAllItems($nestedStoreId)) {
                return 0;
            }
            $excludeItemIds = $this->_getExcludeItemIdsByTemplate($nestedStoreId);
        } elseif ($storeId) {
            if ($this->isAssignForAllItems($this->getAssignType())) {
                $excludeItemIds = $this->_getExcludeItemIdsByTemplate();
            } else {
                $excludeItemIds = false;
            }
        }

        $storeId    = !empty($nestedStoreId) ? $nestedStoreId : $storeId;
        $collection = $this->getBrands();

        if (!$this->getUseForDefaultValue()) {
            $collection->addFieldToFilter('store_id', ['in' => [0, $storeId]]);
        }

        if (self::SCOPE_EMPTY == $this->getScope()) {
            $attributes = $this->getAttributeCodesByType();
            foreach ($attributes as $attributeCode) {
                $collection->getSelect()->where('store_data.' . $attributeCode . ' = ""');
                $collection->getSelect()->where('store_data.store_id =' . $storeId);
            }
        }

        if ($this->isAssignForIndividualItems()) {
            $assignItems = (is_array($this->getBrandsData()) && count(
                    $this->getBrandsData()
                )) ? $this->getBrandsData() : 0;
            $collection->getSelect()->where('main_table.brand_id IN (?)', $assignItems);
        }
        if (!empty($excludeItemIds)) {
            $collection->getSelect()->where('main_table.brand_id NOT IN (?)', $excludeItemIds);
        }

        if ($onlyCountFlag) {
            return $collection->count();
        } else {
            $collection->addFieldToSelect('*');
            $collection->getSelect()->order('main_table.brand_id asc');
            $collection->getSelect()->limit($limit, $from);
            return $collection;
        }
    }

    /**
     * @return mixed
     */
    public function getBrands()
    {
        $this->brands = new DataObject();
        $this->_eventManager->dispatch(
            'mageworx_seoall_brand_transfer_collection',
            ['object' => $this->brands]
        );
        $this->brands = $this->brands->getData('brands');

        return $this->brands;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBrandIdsAssignedForAnalogTemplate()
    {
        /** @var \MageWorx\SeoXTemplates\Model\ResourceModel\Template\Brand\Collection $collection */

        $collection = $this->getCollection()
                           ->addStoreModeFilter($this->getIsSingleStoreMode())
                           ->addSpecificStoreFilter($this->getStoreId())
                           ->addTypeFilter($this->getTypeId())
                           ->addAssignTypeFilter(self::ASSIGN_INDIVIDUAL_ITEMS);

        if ($this->getTemplateId()) {
            $collection->excludeTemplateFilter($this->getTemplateId());
        }
        $templateIDs = $collection->getAllIds();
        return $this->_getResource()->getIndividualItemIds($templateIDs);
    }

    /**
     * @param null|int $nestedStoreId
     * @return array|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getExcludeItemIdsByTemplate($nestedStoreId = null)
    {
        /** @var \MageWorx\SeoXTemplates\Model\ResourceModel\Template\Brand\Collection $collection */
        $templateCollection = $this->getCollection()
                                   ->addStoreModeFilter($this->getIsSingleStoreMode())
                                   ->addTypeFilter($this->getTypeId())
                                   ->addAssignTypeFilter($this->getAssignForIndividualItems());

        if ($this->getStoreId() == '0' && !$this->getIsSingleStoreMode()) {
            if ($this->isAssignForAllItems($this->getAssignType())) {
                $templateCollection->addStoreFilter($nestedStoreId);
            } elseif ($this->isAssignForIndividualItems($this->getAssignType())) {
                $templateCollection->addStoreFilter($nestedStoreId);
                $templateCollection->excludeTemplateFilter($this->getTemplateId());
            }
        } elseif ($this->getStoreId() || $this->getIsSingleStoreMode()) {
            if ($this->isAssignForAllItems($this->getAssignType())) {
                $templateCollection->addSpecificStoreFilter($this->getStoreId());
            } elseif ($this->isAssignForIndividualItems($this->getAssignType())) {
                return false;
            }
        }

        $excludeItemIds = [];
        foreach ($templateCollection as $template) {
            $template->loadItems();
            if (!$template->isAssignForAllItems()) {
                $itemIds = $template->getBrandsData();
                if (is_array($itemIds)) {
                    $excludeItemIds = array_merge($excludeItemIds, $itemIds);
                }
            }
        }

        return !empty($excludeItemIds) ? $excludeItemIds : false;
    }

    /**
     * Calc priority for template
     *
     * @return int
     */
    protected function calcPriority()
    {
        if ($this->getStoreId() == 0) {
            if ($this->getAssignType() == self::ASSIGN_ALL_ITEMS) {
                $priority = 1;
            } elseif ($this->getAssignType() == self::ASSIGN_INDIVIDUAL_ITEMS) {
                $priority = 2;
            }
        } else {
            if ($this->getAssignType() == self::ASSIGN_ALL_ITEMS) {
                $priority = 3;
            } elseif ($this->getAssignType() == self::ASSIGN_INDIVIDUAL_ITEMS) {
                $priority = 4;
            }
        }

        return $priority;
    }
}
