<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */

namespace Amasty\Paction\Model\Command;

use Magento\Framework\App\ResourceConnection;

class Crosssell extends Relate
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
        $this->_type = 'crosssell';

        $this->_info = [
            'confirm_title'   => 'Cross-sell',
            'confirm_message' => 'Are you sure you want to cross-sell?',
            'type'            => $this->_type,
            'label'           => 'Cross-sell',
            'fieldLabel'      => 'Selected To IDs',
            'placeholder'     => 'id1,id2,id3'
        ];
        $this->productRepository = $productRepository;
        $this->saveProductLinks = $saveProductLinks;
        $this->setFieldLabel();
    }
}
