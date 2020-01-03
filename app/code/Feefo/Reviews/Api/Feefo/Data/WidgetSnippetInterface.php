<?php

namespace Feefo\Reviews\Api\Feefo\Data;

/**
 * Interface WidgetSnippetInterface
 */
interface WidgetSnippetInterface
{
    const DATA_SERVICE_SNIPPET          = 'serviceSnippet';

    const DATA_PRODUCT_STARS_SNIPPET    = 'productStarsSnippet';

    const DATA_PRODUCT_BASE_SNIPPET     = 'productBaseSnippet';
    
    const DATA_PRODUCT_LIST_SNIPPET     = 'productListSnippet';

    /**
     * Retrieve a snippet of the service widget
     *
     * @return string
     */
    public function getServiceSnippet();

    /**
     * Configure a snippet of the service widget
     *
     * @param string $snippet
     * @return $this
     */
    public function setServiceSnippet($snippet);

    /**
     * Retrieve a snippet of the product stars widget
     *
     * @return string
     */
    public function getProductStarsSnippet();

    /**
     * Configure a snippet of the product stars widget
     *
     * @param string $snippet
     * @return $this
     */
    public function setProductStarsSnippet($snippet);

    /**
     * Retrieve a snippet of the product base widget
     *
     * @return string
     */
    public function getProductBaseSnippet();

    /**
     * Configure a snippet of the product base widget
     *
     * @param string $snippet
     * @return $this
     */
    public function setProductBaseSnippet($snippet);

    /**
     * Retrieve a snippet of the product list widget
     *
     * @return string
     */
    public function getProductListSnippet();

    /**
     * Configure a snippet of the product list widget
     *
     * @param string $snippet
     * @return $this
     */
    public function setProductListSnippet($snippet);

}