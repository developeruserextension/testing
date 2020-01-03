<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */


namespace Amasty\Paction\Block\Adminhtml\Product\Edit\Action\Attribute\Tab\TierPrice;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Price\Group\AbstractGroup;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\GroupRepositoryInterface;

class Group extends AbstractGroup
{
    /**
     * @var \Magento\Catalog\Model\Config\Source\Product\Options\TierPrice
     */
    private $tierPriceValueType;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        GroupRepositoryInterface $groupRepository,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Registry $registry,
        GroupManagementInterface $groupManagement,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Amasty\Paction\Model\Source\TierPrice $tierPriceValueType,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $groupRepository,
            $directoryHelper,
            $moduleManager,
            $registry,
            $groupManagement,
            $searchCriteriaBuilder,
            $localeCurrency,
            $data
        );
        $this->tierPriceValueType = $tierPriceValueType;
    }

    /**
     * @return array
     */
    public function getPriceValueTypes()
    {
        return $this->tierPriceValueType->toOptionArray();
    }

    /**
     * Check group price attribute scope is global
     *
     * @return bool
     */
    public function isScopeGlobal()
    {
        return true;
    }
}