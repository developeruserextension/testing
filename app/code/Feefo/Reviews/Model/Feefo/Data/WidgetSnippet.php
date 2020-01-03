<?php

namespace Feefo\Reviews\Model\Feefo\Data;

use Feefo\Reviews\Api\Feefo\Data\WidgetSnippetInterface;

/**
 * Class WidgetSnippet
 */
class WidgetSnippet extends JsonableDataObject implements WidgetSnippetInterface
{

    /**
     * Retrieve a snippet of the service widget
     *
     * @return string
     */
    public function getServiceSnippet()
    {
        return $this->getData(static::DATA_SERVICE_SNIPPET);
    }

    /**
     * Configure a snippet of the service widget
     *
     * @param string $snippet
     * @return $this
     */
    public function setServiceSnippet($snippet)
    {
        $this->setData(static::DATA_SERVICE_SNIPPET, $snippet);

        return $this;
    }

    /**
     * Retrieve a snippet of the product stars widget
     *
     * @return string
     */
    public function getProductStarsSnippet()
    {
        return $this->getData(static::DATA_PRODUCT_STARS_SNIPPET);
    }

    /**
     * Configure a snippet of the product stars widget
     *
     * @param string $snippet
     * @return $this
     */
    public function setProductStarsSnippet($snippet)
    {
        $this->setData(static::DATA_PRODUCT_STARS_SNIPPET, $snippet);

        return $this;
    }

    /**
     * Retrieve a snippet of the product base widget
     *
     * @return string
     */
    public function getProductBaseSnippet()
    {
        return $this->getData(static::DATA_PRODUCT_BASE_SNIPPET);
    }

    /**
     * Configure a snippet of the product base widget
     *
     * @param string $snippet
     * @return $this
     */
    public function setProductBaseSnippet($snippet)
    {
        $this->setData(static::DATA_PRODUCT_BASE_SNIPPET, $snippet);

        return $this;
    }

    /**
     * Retrieve a snippet of the product list widget
     *
     * @return string
     */
    public function getProductListSnippet()
    {
        return $this->getData(static::DATA_PRODUCT_LIST_SNIPPET);
    }

    /**
     * Configure a snippet of the product list widget
     *
     * @param string $snippet
     * @return $this
     */
    public function setProductListSnippet($snippet)
    {
        $this->setData(static::DATA_PRODUCT_LIST_SNIPPET, $snippet);

        return $this;
    }
}