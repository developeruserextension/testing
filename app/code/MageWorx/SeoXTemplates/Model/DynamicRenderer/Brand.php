<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\DynamicRenderer;

class Brand
{
    /**
     * @var string
     */
    protected $isConvertedTitle;

    /**
     * @var string
     */
    protected $isConvertedMetaDescription;

    /**
     * @var string
     */
    protected $isConvertedMetaKeywords;

    /**
     * @var string
     */
    protected $isConvertedTexts;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Brand\MetaTitle
     */
    protected $metaTitleConverter;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Brand\MetaDescription
     */
    protected $metaDescriptionConverter;

    /**
     * @var \MageWorx\SeoXTemplates\Model\Converter\Brand\MetaKeywords
     */
    protected $metaKeywordsConverter;

    /**
     * @var \Zend\Filter\StripTags
     */
    protected $stripTags;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Brand constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\SeoXTemplates\Model\Converter\Brand\MetaTitle $metaTitleConverter
     * @param \MageWorx\SeoXTemplates\Model\Converter\Brand\MetaDescription $metaDescriptionConverter
     * @param \MageWorx\SeoXTemplates\Model\Converter\Brand\MetaKeywords $metaKeywordsConverter
     * @param \Zend\Filter\StripTags $stripTags
     * @param \Magento\Framework\View\Page\Config $pageConfig
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\SeoXTemplates\Model\Converter\Brand\MetaTitle $metaTitleConverter,
        \MageWorx\SeoXTemplates\Model\Converter\Brand\MetaDescription $metaDescriptionConverter,
        \MageWorx\SeoXTemplates\Model\Converter\Brand\MetaKeywords $metaKeywordsConverter,
        \Zend\Filter\StripTags $stripTags,
        \Magento\Framework\View\Page\Config $pageConfig
    ) {
        $this->storeManager             = $storeManager;
        $this->metaTitleConverter       = $metaTitleConverter;
        $this->metaDescriptionConverter = $metaDescriptionConverter;
        $this->metaKeywordsConverter    = $metaKeywordsConverter;
        $this->stripTags                = $stripTags;
        $this->pageConfig               = $pageConfig;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $brand
     * @return bool
     */
    public function modifyBrandTitle($brand)
    {
        if ($this->isConvertedTitle) {
            return true;
        }

        $this->isConvertedTitle = true;
        $title                  = $this->metaTitleConverter->convert($brand, $brand->getMetaTitle(), true);

        if (!empty($title)) {
            $title = trim(htmlspecialchars(html_entity_decode($title, ENT_QUOTES, 'UTF-8')));
            if ($title) {
                $this->pageConfig->getTitle()->set($title);

                return true;
            }
        };

        return false;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $brand
     * @return boolean
     */
    public function modifyBrandMetaDescription($brand)
    {
        if ($this->isConvertedMetaDescription) {
            return true;
        }

        $metaDescription = $this->metaDescriptionConverter
            ->convert($brand, $brand->getMetaDescription(), true);

        if (!empty($metaDescription)) {
            $metaDescription = htmlspecialchars(
                html_entity_decode(
                    preg_replace(
                        ['/\r?\n/', '/[ ]{2,}/'],
                        [' ', ' '],
                        $this->stripTags->filter($metaDescription)
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
            if ($metaDescription) {
                $this->isConvertedMetaDescription = $metaDescription;
                $this->pageConfig->setDescription($metaDescription);

                return true;
            }
        }

        return false;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $brand
     * @return boolean
     */
    public function modifyBrandMetaKeywords($brand)
    {
        if ($this->isConvertedMetaKeywords) {
            return true;
        }

        $metaKeywords = $this->metaKeywordsConverter
            ->convert($brand, $brand->getMetaKeywords(), true);

        if (!empty($metaKeywords)) {
            $metaKeywords = htmlspecialchars(
                html_entity_decode(
                    preg_replace(
                        ['/\r?\n/', '/[ ]{2,}/'],
                        [' ', ' '],
                        $this->stripTags->filter($metaKeywords)
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
            if ($metaKeywords) {
                $this->isConvertedMetaKeywords = $metaKeywords;
                $this->pageConfig->setKeywords($metaKeywords);

                return true;
            }
        }

        return false;
    }
}
