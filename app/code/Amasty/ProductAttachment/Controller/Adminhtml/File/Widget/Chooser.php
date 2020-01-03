<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Controller\Adminhtml\File\Widget;

use Magento\Framework\Controller\ResultFactory;

class Chooser extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Amasty_ProductAttachment::files_list';

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * Chooser Source action
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Raw $resultRaw */
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);

        $uniqId = $this->getRequest()->getParam('uniq_id');
        $layout = $this->layoutFactory->create();
        $filesGrid = $layout->createBlock(
            \Amasty\ProductAttachment\Block\Adminhtml\Widget\Grid::class,
            '',
            [
                'data' => [
                    'id' => $uniqId,
                    'use_massaction' => false
                ]
            ]
        );

        return $resultRaw->setContents($filesGrid->toHtml());
    }
}
