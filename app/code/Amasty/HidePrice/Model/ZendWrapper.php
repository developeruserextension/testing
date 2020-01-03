<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_HidePrice
 */


namespace Amasty\HidePrice\Model;

class ZendWrapper
{
    /**
     * @var null|\Zend\Dom\Query|\Zend_Dom_Query
     */
    private $domQuery = null;

    /**
     * @var string
     */
    private $executeMethod = 'query';

    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * @param \DOMDocument $domDocument
     */
    public function setContent(\DOMDocument $domDocument)
    {
        if (class_exists(\Zend_Dom_Query::class)) {
            $this->domQuery = new \Zend_Dom_Query($domDocument);
            $this->initialized = true;
        } elseif (class_exists(\Zend\Dom\Query::class)) {
            $this->domQuery = new \Zend\Dom\Query($domDocument->saveHTML());
            $this->executeMethod = 'execute';
            $this->initialized = true;
        }
    }

    /**
     * @param string $selector
     *
     * @return \Zend_Dom_Query_Result|\Zend\Dom\NodeList|array
     */
    public function query($selector)
    {
        if ($this->domQuery) {
            $result = $this->domQuery->{$this->executeMethod}($selector);
        } else {
            $result = [];
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getDocument()
    {
        if ($this->domQuery) {
            /** @var string|\DOMDocument $document */
            $document = $this->domQuery->getDocument();
            if (!is_string($document)) {
                $document = $document->saveHTML();
            }
        } else {
            $document = '';
        }

        return $document;
    }

    /**
     * @return bool
     */
    public function isInitialized()
    {
        return $this->initialized;
    }
}
