<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Icon;

use Amasty\ProductAttachment\Model\Filesystem\UrlResolver;

class GetIconForFile
{
    /**
     * @var ResourceModel\Icon
     */
    private $iconResource;

    /**
     * @var UrlResolver
     */
    private $urlResolver;

    public function __construct(
        ResourceModel\Icon $iconResource,
        UrlResolver $urlResolver
    ) {
        $this->iconResource = $iconResource;
        $this->urlResolver = $urlResolver;
    }

    //TODO FileStatInterface
    public function byFileName($filename)
    {
        if (!empty($filename)) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            if (!empty($extension) && $iconImage = $this->iconResource->getExtensionIconImage($extension)) {
                return $this->urlResolver->getIconUrlByName($iconImage);
            }
        }

        return false;
    }

    public function byFileExtension($ext)
    {
        if (!empty($ext)) {
            if ($iconImage = $this->iconResource->getExtensionIconImage($ext)) {
                return $this->urlResolver->getIconUrlByName($iconImage);
            }
        }

        return false;
    }
}
