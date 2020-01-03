<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Model\History;

use Amasty\StoreCredit\Api\Data\HistoryInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel;

class History extends AbstractModel implements HistoryInterface
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        Context $context,
        \Magento\Framework\Registry $registry,
        ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->priceCurrency = $priceCurrency;
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\StoreCredit\Model\History\ResourceModel\History::class);
        $this->setIdFieldName(HistoryInterface::HISTORY_ID);
    }

    /**
     * @inheritdoc
     */
    public function getHistoryId()
    {
        return (int)$this->_getData(HistoryInterface::HISTORY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setHistoryId($historyId)
    {
        return $this->setData(HistoryInterface::HISTORY_ID, (int)$historyId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerHistoryId()
    {
        return (int)$this->_getData(HistoryInterface::CUSTOMER_HISTORY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerHistoryId($customerHistoryId)
    {
        return $this->setData(HistoryInterface::CUSTOMER_HISTORY_ID, (int)$customerHistoryId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return (int)$this->_getData(HistoryInterface::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(HistoryInterface::CUSTOMER_ID, (int)$customerId);
    }

    /**
     * @inheritdoc
     */
    public function isDeduct()
    {
        return (bool)$this->_getData(HistoryInterface::IS_DEDUCT);
    }

    /**
     * @inheritdoc
     */
    public function setIsDeduct($isDeduct)
    {
        return $this->setData(HistoryInterface::IS_DEDUCT, (bool)$isDeduct);
    }

    /**
     * @inheritdoc
     */
    public function getDifference()
    {
        return (float)$this->_getData(HistoryInterface::DIFFERENCE);
    }

    /**
     * @param null|string|bool|int|\Magento\Framework\App\ScopeInterface $scope
     * @param \Magento\Framework\Model\AbstractModel|string|null $currency
     *
     * @return string
     */
    public function getFormatDifference($scope = null, $currency = null)
    {
        return $this->priceCurrency->convertAndFormat(
            $this->_getData(HistoryInterface::DIFFERENCE),
            false,
            2,
            $scope,
            $currency
        );
    }

    /**
     * @param null|string|bool|int|\Magento\Framework\App\ScopeInterface $scope
     * @param \Magento\Framework\Model\AbstractModel|string|null $currency
     *
     * @return string
     */
    public function getAbsFormatDifference($scope = null, $currency = null)
    {
        return $this->priceCurrency->convertAndFormat(
            abs($this->_getData(HistoryInterface::DIFFERENCE)),
            false,
            2,
            $scope,
            $currency
        );
    }

    /**
     * @inheritdoc
     */
    public function setDifference($difference)
    {
        return $this->setData(HistoryInterface::DIFFERENCE, (float)$difference);
    }

    /**
     * @inheritdoc
     */
    public function getStoreCreditBalance()
    {
        return (float)$this->_getData(HistoryInterface::STORE_CREDIT_BALANCE);
    }

    /**
     * @param null|string|bool|int|\Magento\Framework\App\ScopeInterface $scope
     * @param \Magento\Framework\Model\AbstractModel|string|null $currency
     *
     * @return string
     */
    public function getFormatStoreCreditBalance($scope = null, $currency = null)
    {
        return $this->priceCurrency->convertAndFormat(
            $this->_getData(HistoryInterface::STORE_CREDIT_BALANCE),
            false,
            2,
            $scope,
            $currency
        );
    }

    /**
     * @inheritdoc
     */
    public function setStoreCreditBalance($storeCreditBalance)
    {
        return $this->setData(HistoryInterface::STORE_CREDIT_BALANCE, (float)$storeCreditBalance);
    }

    /**
     * @inheritdoc
     */
    public function getAction()
    {
        return (int)$this->_getData(HistoryInterface::ACTION);
    }

    /**
     * @inheritdoc
     */
    public function setAction($action)
    {
        return $this->setData(HistoryInterface::ACTION, (int)$action);
    }

    /**
     * @inheritdoc
     */
    public function getActionData()
    {
        return $this->_getData(HistoryInterface::ACTION_DATA);
    }

    /**
     * @inheritdoc
     */
    public function setActionData($actionData)
    {
        return $this->setData(HistoryInterface::ACTION_DATA, $actionData);
    }

    /**
     * @inheritdoc
     */
    public function getMessage()
    {
        return $this->_getData(HistoryInterface::MESSAGE);
    }

    /**
     * @inheritdoc
     */
    public function setMessage($message)
    {
        return $this->setData(HistoryInterface::MESSAGE, $message);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(HistoryInterface::CREATED_AT);
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getFullActionMessage()
    {
        return MessageProcessor::processFull(
            $this->getAction(),
            [
                array_merge(
                    [$this->getFormatDifference(), $this->getFormatStoreCreditBalance()],
                    json_decode($this->getActionData(), true)
                )
            ]
        );
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getMailActionMessage()
    {
        return MessageProcessor::processMail(
            $this->getAction(),
            [
                array_merge(
                    [$this->getFormatDifference(), $this->getFormatStoreCreditBalance()],
                    json_decode($this->getActionData(), true)
                )
            ]
        );
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getSmallActionMessage()
    {
        return MessageProcessor::processSmall(
            $this->getAction(),
            [
                array_merge(
                    [$this->getFormatDifference(), $this->getFormatStoreCreditBalance()],
                    json_decode($this->getActionData(), true)
                )
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(HistoryInterface::STORE_ID, $storeId);
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->_getData(HistoryInterface::STORE_ID);
    }
}
