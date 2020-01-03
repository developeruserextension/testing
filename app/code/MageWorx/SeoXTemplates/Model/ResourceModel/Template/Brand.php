<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\ResourceModel\Template;

use MageWorx\SeoXTemplates\Model\Template\Brand as TemplateBrandModel;

/**
 * brand page template mysql resource
 */
class Brand extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     * @var string
     */
    protected $brandRelationTable = 'mageworx_seoxtemplates_template_relation_brand';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoxtemplates_template_brand', 'template_id');
    }

    /**
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->loadItems();

        return parent::_afterLoad($object);
    }

    /**
     * Process template data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['template_id = ?' => (int)$object->getId()];

        $this->getConnection()->delete($this->getTable($this->brandRelationTable), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * @param TemplateBrandModel $template
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveBrandRelation(TemplateBrandModel $template)
    {
        $id           = $template->getId();
        $brands = $template->getBrandsData();
        $this->clearAllRelations($template);
        if (!empty($brands)) {
            $data = [];
            foreach ($brands as $brandId) {
                $data[] = [
                    'template_id'    => (int)$id,
                    'brand_id' => (int)$brandId,
                ];
            }
            $this->getConnection()->insertMultiple($this->getTable($this->brandRelationTable), $data);
        }

        return $this;
    }

    /**
     * @param TemplateBrandModel $template
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function clearBrandRelation(TemplateBrandModel $template)
    {
        $id        = $template->getId();
        $condition = ['template_id=?' => $id];
        $this->getConnection()->delete($this->getTable($this->brandRelationTable), $condition);
        $template->setIsChangedBrandList(true);

        return $this;
    }

    /**
     * @param TemplateBrandModel $template
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function clearAllRelations(TemplateBrandModel $template)
    {
        $this->clearBrandRelation($template);

        return $this;
    }

    /**
     * Retrieve individual item ids by template(s)
     *
     * @param int|array $templateId
     * @return array
     */
    public function getIndividualItemIds($templateId)
    {
        if (!is_array($templateId)) {
            $templateId = [$templateId];
        }

        $select = $this->getConnection()
                       ->select()
                       ->from(
                           $this->getTable($this->brandRelationTable),
                           new \Zend_Db_Expr("DISTINCT `brand_id`")
                       )
                       ->where('template_id IN (?)', $templateId);

        $result = [];
        $data   = $this->getConnection()->fetchAssoc($select);
        if ($data && is_array($data)) {
            $result = array_keys($data);
        }

        return $result;
    }
}
