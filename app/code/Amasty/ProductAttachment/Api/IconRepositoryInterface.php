<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Api;

interface IconRepositoryInterface
{
    /**
     * Save icon.
     *
     * @param \Amasty\ProductAttachment\Api\Data\IconInterface $icon
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return \Amasty\ProductAttachment\Api\Data\IconInterface
     */
    public function save(\Amasty\ProductAttachment\Api\Data\IconInterface $icon);

    /**
     * Retrieve icon.
     *
     * @param int $iconId
     * @return \Amasty\ProductAttachment\Api\Data\IconInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($iconId);

    /**
     * Retrieve icons matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Amasty\ProductAttachment\Api\Data\IconInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete attachment.
     *
     * @param \Amasty\ProductAttachment\Api\Data\IconInterface $icon
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Amasty\ProductAttachment\Api\Data\IconInterface $icon);

    /**
     * Delete icon by ID.
     *
     * @param int $iconId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($iconId);
}
