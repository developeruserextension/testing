<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */

namespace Amasty\Paction\Model\Command;

use Magento\Framework\App\ResourceConnection;

class Upsell extends Relate
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\Product\Link\SaveHandler
     */
    protected $saveProductLinks;

    public function __construct(
        \Amasty\Paction\Helper\Data $helper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\Data\ProductLinkInterfaceFactory $productLinkInterfaceFactory,
        \Magento\Catalog\Model\Product\Link\SaveHandler $saveProductLinks,
        ResourceConnection $resource
    ) {
        parent::__construct(
            $helper,
            $productRepository,
            $productLinkInterfaceFactory,
            $saveProductLinks,
            $resource
        );
        $this->_type = 'upsell';
        $this->_info = [
            'confirm_title'   => 'Up-sell',
            'confirm_message' => 'Are you sure you want to up-sell?',
            'type'            => $this->_type,
            'label'           => 'Up-sell',
            'placeholder'     => 'id1,id2,id3',
            'fieldLabel'      => ''
        ];
        $this->productRepository = $productRepository;
        $this->saveProductLinks = $saveProductLinks;
        $this->setFieldLabel();
    }
}
