<?php

namespace Feefo\Reviews\Api\Feefo;

use Feefo\Reviews\Api\Feefo\Data\WidgetWrapperInterface;

/**
 * Interface WidgetInterface
 *
 * Service Contract that describes widgets API of Feefo service
 */
interface WidgetInterface
{
    /**
     * Retrieve the settings of the widgets
     *
     * @return WidgetWrapperInterface
     */
    public function getSettings();
}