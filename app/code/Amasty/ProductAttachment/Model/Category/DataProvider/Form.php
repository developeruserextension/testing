<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Category\DataProvider;

class Form
{
    /**
     * @var Modifiers\Meta
     */
    private $metaModifier;

    /**
     * @var Modifiers\Data
     */
    private $dataModifier;

    public function __construct(
        Modifiers\Meta $metaModifier,
        Modifiers\Data $dataModifier
    ) {

        $this->metaModifier = $metaModifier;
        $this->dataModifier = $dataModifier;
    }

    /**
     * @param \Magento\Catalog\Model\Category\DataProvider $subject
     * @param array $meta
     *
     * @return array
     */
    public function afterGetMeta($subject, $meta)
    {
        return $this->metaModifier->execute($meta);
    }

    /**
     * @param \Magento\Catalog\Model\Category\DataProvider $subject
     * @param $data
     *
     * @return array
     */
    public function afterGetData($subject, $data)
    {
        return $this->dataModifier->execute($data);
    }
}
