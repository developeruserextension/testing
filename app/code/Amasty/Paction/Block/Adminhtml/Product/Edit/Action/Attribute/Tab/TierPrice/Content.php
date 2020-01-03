<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */


namespace Amasty\Paction\Block\Adminhtml\Product\Edit\Action\Attribute\Tab\TierPrice;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\GroupRepositoryInterface;

class Content extends Group
{
    /**
     * Content constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param GroupRepositoryInterface $groupRepository
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\Registry $registry
     * @param GroupManagementInterface $groupManagement
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Amasty\Paction\Model\Source\TierPrice $tierPriceValueType
     * @param array $data
     */
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
        parent::__construct($context, $groupRepository, $directoryHelper, $moduleManager, $registry, $groupManagement, $searchCriteriaBuilder, $localeCurrency, $tierPriceValueType, $data);
    }

    /**
     * @var string
     */
    protected $_template = 'tier_prices.phtml';

    /**
     * Prepare global layout
     * Add "Add" button to layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            ['label' => __('Add'), 'onclick' => 'return tierPriceControl.addItem()', 'class' => 'add']
        );
        $button->setName('add_tier_price_item_button');

        $this->setChild('add_button', $button);

        return parent::_prepareLayout();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAllGroupsId()
    {
        return [$this->_groupManagement->getAllCustomersGroup()->getId() => __('ALL GROUPS')];
    }
}
