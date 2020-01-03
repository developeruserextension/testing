<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Icon;

use Amasty\ProductAttachment\Api\Data\IconInterface;
use Amasty\ProductAttachment\Api\IconRepositoryInterface;
use Amasty\ProductAttachment\Model\Icon\IconFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;

class Repository implements IconRepositoryInterface
{
    /**
     * @var IconFactory
     */
    private $iconFactory;

    /**
     * @var ResourceModel\Icon
     */
    private $iconResource;

    /**
     * @var IconInterface[]
     */
    private $icons;

    public function __construct(
        IconFactory $iconFactory,
        ResourceModel\Icon $iconResource
    ) {
        $this->iconFactory = $iconFactory;
        $this->iconResource = $iconResource;
    }

    /**
     * Save icon.
     *
     * @param \Amasty\ProductAttachment\Api\Data\IconInterface $icon
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return \Amasty\ProductAttachment\Api\Data\IconInterface
     */
    public function save(\Amasty\ProductAttachment\Api\Data\IconInterface $icon)
    {
        try {
            if ($icon->getIconId()) {
                $icon = $this->getById($icon->getIconId())->addData($icon->getData());
            }
            $this->iconResource->save($icon);
            unset($this->icons[$icon->getIconId()]);
        } catch (\Exception $e) {
            if ($icon->getIconId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save icon with ID %1. Error: %2',
                        [$icon->getIconId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new icon. Error: %1', $e->getMessage()));
        }

        return $icon;
    }

    /**
     * Retrieve icon.
     *
     * @param int $iconId
     *
     * @return \Amasty\ProductAttachment\Api\Data\IconInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($iconId)
    {
        if (!isset($this->icons[$iconId])) {
            /** @var \Amasty\ProductAttachment\Model\Icon\Icon $icon */
            $icon = $this->iconFactory->create();
            $this->iconResource->load($icon, $iconId);
            if (!$icon->getIconId()) {
                throw new NoSuchEntityException(__('Icon with specified ID "%1" not found.', $iconId));
            }
            $this->icons[$iconId] = $icon;
        }

        return $this->icons[$iconId];
    }

    /**
     * Retrieve icons matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Amasty\ProductAttachment\Api\Data\IconInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        // TODO: Implement getList() method.
    }

    /**
     * Delete attachment.
     *
     * @param \Magento\Framework\Model\AbstractModel|\Amasty\ProductAttachment\Api\Data\IconInterface $icon
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Amasty\ProductAttachment\Api\Data\IconInterface $icon)
    {
        try {
            $this->iconResource->delete($icon);
            unset($this->icons[$icon->getIconId()]);
        } catch (\Exception $e) {
            if ($icon->getIconId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove icon with ID %1. Error: %2',
                        [$icon->getIconId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove icon. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * Delete icon by ID.
     *
     * @param int $iconId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($iconId)
    {
        if (!($icon = $this->getById($iconId))) {
            throw new NoSuchEntityException(__('Icon with specified ID "%1" not found.', $iconId));
        } else {
            $this->delete($icon);

            return true;
        }
    }
}
