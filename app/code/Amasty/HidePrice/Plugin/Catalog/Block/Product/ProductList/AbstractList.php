<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Plugin\Catalog\Block\Product\ProductList;

use Amasty\HidePrice\Helper\Data;
use Amasty\HidePrice\Model\Source\HideButton;
use Amasty\HidePrice\Model\ZendWrapper;
use Amasty\HidePrice\Model\ZendWrapperFactory;

class AbstractList
{
    private $replacedJs;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var ZendWrapperFactory
     */
    private $zendWrapperFactory;

    public function __construct(
        Data $helper,
        \Magento\Framework\Registry $coreRegistry,
        ZendWrapperFactory $zendWrapperFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->coreRegistry = $coreRegistry;
        $this->logger = $logger;
        $this->zendWrapperFactory = $zendWrapperFactory;
    }

    /**
     * replace "add to cart" button if it needed
     *
     * @param string $html
     * @return string
     */
    protected function replaceButtonFromHtml($html)
    {
        if ($this->helper->getModuleConfig('information/hide_button') && $html) {
            try {
                $this->replacedJs = [];
                if (strpos($html, '<script') !== false) {
                    $html = preg_replace_callback(
                        '#(\<script[^\>]*\>)(.*?)(\<\/script\>)#ims',
                        [$this, 'replaceJS'],
                        $html
                    );
                }

                $dom = new \DOMDocument('1.0', 'HTML-ENTITIES');
                libxml_use_internal_errors(true);
                $charset = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
                $dom->loadHTML($charset . $html);

                /** @var ZendWrapper $domQuery */
                $domQuery = $this->zendWrapperFactory->create();
                $domQuery->setContent($dom);

                if ($domQuery->isInitialized()) {
                    foreach ($this->getProductsData() as $productData) {
                        $result = $domQuery->query(
                            $this->generateSelector($productData)
                        );

                        if ($result && count($result)) {
                            $domDoc = $result->getDocument();
                            $result->rewind();
                            $result = $result->current();
                            $replacement = $this->helper->getNewAddToCartHtml(null, $productData);

                            if ($this->helper->getModuleConfig('information/hide_button') == HideButton::HIDE
                                && $result->parentNode
                                && $result->parentNode->tagName == 'form'
                            ) {
                                $result->parentNode->removeChild($result);
                            } else {
                                $replacementElement = $result->ownerDocument->createDocumentFragment();
                                $replacementElement->appendXML(htmlentities($replacement));
                                $result->parentNode->replaceChild($replacementElement, $result);
                            }
                            $domQuery->setContent($domDoc);
                        }
                    }

                    $html = $this->getHtml($domQuery->getDocument());
                }
                $html = html_entity_decode($html);
                $html = str_replace($charset, '', $html);

                foreach ($this->replacedJs as $key => $js) {
                    $html = str_replace('{{HIDEPRICE_' . $key . '}}', $js, $html);
                }
            } catch (\Exception $exception) {
                $this->logger->critical($exception->getMessage());
            }
        }

        return $html;
    }

    /**
     * @param string $docDocument
     * @return mixed|null|string|string[]
     */
    private function getHtml($docDocument)
    {
        $fragment = str_replace(
            ['<html>', '</html>', '<body>', '</body>', '<head>', '</head>'],
            '',
            $docDocument
        );

        $fragment = preg_replace('/^<!DOCTYPE.+?>/', '', $fragment);

        return $fragment;
    }

    /**
     * @param $matches
     * @return string
     */
    private function replaceJS($matches)
    {
        $text = '';
        if (count($matches) >= 4) {
            $this->replacedJs[] = $matches[2];
            $text = $matches[1] . '{{HIDEPRICE_' . (count($this->replacedJs) - 1) . '}}' . $matches[3];
        } elseif (isset($matches[0])) {
            $text = $matches[0];
        }

        return $text;
    }

    /**
     * @return array
     */
    private function getProductsData()
    {
        $dataArray = $this->coreRegistry->registry('amasty_hideprice_data_array');
        if (!$dataArray || !is_array($dataArray)) {
            $dataArray = [];
        }

        return $dataArray;
    }

    /**
     * get selector for one product
     *
     * @param array $productData
     * @return string
     */
    private function generateSelector($productData)
    {
        return 'form[data-product-sku="' . $productData['sku']
            . '"] > button, form[data-role="tocart-form"][action*="product/' . $productData['id']
            . '"] > button';
    }
}
