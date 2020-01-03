<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Block\Adminhtml;

use Amasty\StoreCredit\Api\Data\StoreCreditInterface;
use Amasty\StoreCredit\Api\StoreCreditRepositoryInterface;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Customer\Controller\RegistryConstants;

class StoreCreditTab extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    private $formFactory;

    /**
     * @var StoreCreditRepositoryInterface
     */
    private $storeCreditRepository;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Amasty\StoreCredit\Api\StoreCreditRepositoryInterface $storeCreditRepository,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->formFactory = $formFactory;
        $this->storeCreditRepository = $storeCreditRepository;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('amstorecredit/storecredit/index', ['_current' => true]);
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        if (!$this->canShowTab()) {
            return $this;
        }
        /** @var \Magento\Framework\Data\Form $form **/
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('_storecredit');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Store Credit')]);

        $fieldset->addField(
            StoreCreditInterface::STORE_CREDIT,
            'label',
            [
                'label' => __('Current Balance'),
                'data-form-part' => 'customer_form',
                'name' => 'store_credit',
            ]
        );

        $symbol = $this->_storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
        $fieldset->addField(
            StoreCreditInterface::ADD_OR_SUBTRACT,
            'text',
            [
                'label' => __(
                    'Add or substract a credit value %1',
                    $symbol
                ),
                'name' => StoreCreditInterface::ADD_OR_SUBTRACT,
                'data-form-part' => 'customer_form',
                'note' => __(
                    'You can add or substract an amount from customer\'s balance by entering a number. ' .
                        'For example, enter "99.5" to add %199.5 and "-99.5" to subtract %199.5',
                    $symbol
                )
            ]
        );

        $fieldset->addField(
            StoreCreditInterface::ADMIN_COMMENT,
            'textarea',
            [
                'label' => __('Comment'),
                'data-form-part' => 'customer_form',
                'name' => StoreCreditInterface::ADMIN_COMMENT,
            ]
        );

        $this->setForm($form);
        return $this;
    }

    /**
     * Initialize form fields values
     * Method will be called after prepareForm and can be used for field values initialization
     *
     * @return $this
     */
    protected function _initFormValues()
    {
        $storeCredit = $this->storeCreditRepository->getByCustomerId(
            $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)
        );
        $this->getForm()->addValues(
            [
                StoreCreditInterface::STORE_CREDIT => $this->priceCurrency->convertAndFormat(
                    $storeCredit->getStoreCredit(),
                    false,
                    2
                )
            ]
        );

        return parent::_initFormValues();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->canShowTab()) {
            return parent::_toHtml() . $this->getChildHtml('amstorecredit-history');
        } else {
            return '';
        }
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Store Credit & Refunds');
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return $this->canShowTab();
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __(' Store Credit & Refunds');
    }
}
