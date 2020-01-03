<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */


namespace Amasty\Paction\Model\Command;

class Updateadvancedprices extends \Amasty\Paction\Model\Command
{
    /**
     * @var \Magento\Framework\App\Response\Http
     */
    private $response;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $url;

    /**
     * @var \Magento\Catalog\Helper\Product\Edit\Action\Attribute
     */
    private $attributeHelper;

    public function __construct(
        \Magento\Framework\App\Response\Http $response,
        \Magento\Backend\Model\UrlInterface $url,
        \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeHelper
    ) {
        $this->_type = 'updateadvancedprices';
        $this->_info = [
            'confirm_title' => 'Update Advanced Prices',
            'confirm_message' => 'Are you sure you want to update prices?',
            'type' => $this->_type,
            'label' => 'Update Advanced Prices',
            'fieldLabel' => ''
        ];
        $this->response = $response;
        $this->url = $url;
        $this->attributeHelper = $attributeHelper;
    }

    /**
     * @param $ids
     * @param $storeId
     * @param $val
     *
     * @return $this|\Magento\Framework\App\Response\HttpInterface
     *
     * @throws \Amasty\Paction\Model\CustomException
     */
    public function execute($ids, $storeId, $val)
    {
        if (!is_array($ids)) {
            throw new \Amasty\Paction\Model\CustomException(__('Please select product(s)'));
        }

        $url = $this->url->getUrl(
            'catalog/product_action_attribute/edit',
            ['_current' => true, 'active_tab' => 'tier_prices']
        );

        $this->attributeHelper->setProductIds($ids);

        return $this->response->setRedirect($url);
    }
}
