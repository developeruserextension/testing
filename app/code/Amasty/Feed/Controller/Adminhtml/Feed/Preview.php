<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Controller\Adminhtml\Feed;

use Amasty\Feed\Api\Data\FeedInterface;
use Amasty\Feed\Api\Data\ValidProductsInterface;
use Amasty\Feed\Api\FeedRepositoryInterface;
use Amasty\Feed\Api\ValidProductsRepositoryInterface;
use Amasty\Feed\Controller\Adminhtml\AbstractFeed;
use Amasty\Feed\Model\Config;
use Amasty\Feed\Model\FeedExport;
use Magento\Backend\App\Action;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Psr\Log\LoggerInterface;

/**
 * Class Preview
 *
 * @package Amasty\Feed
 */
class Preview extends AbstractFeed
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var FeedRepositoryInterface
     */
    private $feedRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * @var ValidProductsRepositoryInterface
     */
    private $vProductsRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var FeedExport
     */
    private $feedExport;

    public function __construct(
        Action\Context $context,
        Config $config,
        FeedRepositoryInterface $feedRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        ValidProductsRepositoryInterface $vProductsRepository,
        FeedExport $feedExport,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->feedRepository = $feedRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->vProductsRepository = $vProductsRepository;
        $this->logger = $logger;
        $this->feedExport = $feedExport;
    }

    /**
     * Use only one page
     */
    const PAGE = 0;

    public function execute()
    {
        $items = $this->config->getItemsForPreview() ?: 1;
        $feedId = $this->getRequest()->getParam('id');
        $response = [];

        try {
            /** @var FeedInterface $feed */
            $feed = $this->feedRepository->getById($feedId);

            /** @codingStandardsIgnoreStart */
            //Generate random file name for preview file
            $feed->setFilename(md5(uniqid(rand(), true)));
            /** @codingStandardsIgnoreEnd */

            /** @var \Magento\Framework\Api\SearchCriteria $searchCriteria */
            $searchCriteria = $this->criteriaBuilder->addFilter(
                ValidProductsInterface::FEED_ID,
                $feedId
            )->setPageSize($items)->setCurrentPage(self::PAGE + 1)->create();
            $validProducts = $this->vProductsRepository->getList($searchCriteria);
            $productCount = count($validProducts->getItems());

            if ($productCount === 0) {
                throw new NotFoundException(__('There are no products to generate feed.'
                    . 'Please check Amasty Feed indexers status or feed conditions.'));
            }

            $response['fileType'] = $feed->getFeedType();
            $response['items'] = $productCount;
            $response['content'] = $this->feedExport->export(
                $feed,
                self::PAGE,
                $validProducts->getItems(),
                true,
                true
            );
        } catch (\Exception $exception) {
            $response['error'] = true;
            $response['message'] = $exception->getMessage();

            $this->logger->error($exception->getMessage());
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($response);

        return $resultJson;
    }
}
