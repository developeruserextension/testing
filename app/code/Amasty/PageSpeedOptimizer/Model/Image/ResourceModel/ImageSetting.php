<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_PageSpeedOptimizer
 */


namespace Amasty\PageSpeedOptimizer\Model\Image\ResourceModel;

use Amasty\PageSpeedOptimizer\Api\Data\ImageSettingInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ImageSetting extends AbstractDb
{
    const TABLE_NAME = 'amasty_page_speed_optimizer_image_setting';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ImageSettingInterface::IMAGE_SETTING_ID);
    }
}
