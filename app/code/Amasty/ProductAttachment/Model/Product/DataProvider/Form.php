<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Product\DataProvider;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

class Form extends AbstractModifier
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
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        return $this->metaModifier->execute($meta);
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        return $this->dataModifier->execute($data);
    }
}
