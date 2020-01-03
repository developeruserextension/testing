<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */


namespace Amasty\Paction\Model\Command;

use Magento\Setup\Exception;

class Changevisibility extends \Amasty\Paction\Model\Command
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;

    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        parent::__construct();
        $this->productRepository = $productRepository;

        $this->_type = 'changevisibility';
        $this->_info = [
            'confirm_title'   => 'Change Visibility',
            'confirm_message' => 'Are you sure you want to change visibility?',
            'type'            => 'changevisibility',
            'label'           => 'Change Visibility',
            'fieldLabel'      => 'To'
        ];
    }

    /**
     * @param array $ids
     * @param int $storeId
     * @param string $val
     *
     * @return string success
     * @throws \Magento\Setup\Exception
     */
    public function execute($ids, $storeId, $val)
    {
        $success = '';
        $visibilily = intVal(trim($val));
        $num = 0;
        foreach ($ids as $productId) {
            $product = $this->productRepository->getById($productId)
                ->setStoreId($storeId);
            try {
                $product->setVisibility($visibilily)->save();
                ++$num;
            } catch (\Exception $e) {
                $this->_errors[] = __(
                    'Can not change visibility for product ID %1, error is:',
                    $e->getMessage()
                );
            }
        }

        if ($num) {
            $success = __('Total of %1 products(s) have been successfully updated.', $num);
        }

        return $success;
    }
}
