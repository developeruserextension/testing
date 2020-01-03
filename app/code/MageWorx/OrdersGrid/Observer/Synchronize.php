<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MageWorx\OrdersGrid\Model\Synchronizer;

/**
 * Class Synchronize
 * @package MageWorx\OrdersGrid\Observer
 *
 * Aggregated observer class for the automatically synchronization of additional table of the orders grid
 * All of the observed classes (entities) could be found in the
 * @see \MageWorx\OrdersGrid\Observer\Synchronize::CLASS_MAPPER
 */
class Synchronize extends Synchronizer implements ObserverInterface
{
    /**
     * For the orders:
     * Used on {model_event_prefix}_after_commit_callback
     * @see \Magento\Framework\Model\AbstractModel::afterCommitCallback()
     *
     * For others:
     * Used on {model_event_prefix}_save_after
     * @see \Magento\Framework\Model\AbstractModel::afterSave()
     *
     * @important The Item & Address does not trigger full order synchronization process because it causes transaction
     * errors and breaks a checkout
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        /** @see \Magento\Framework\Model\AbstractModel::_getEventData() */
        $object = $event->getDataObject();
        if (!is_object($object)) {
            return $this;
        }

        foreach (static::CLASS_MAPPER as $className => $method) {
            if ($object instanceof $className) {
                try {
                    $this->{$method}($object);
                } catch (\Exception $exception) {
                    $this->logger->critical($exception->getMessage() . '  ' . $exception->getTraceAsString());
                    continue;
                }
            }
        }

        return $this;
    }
}
