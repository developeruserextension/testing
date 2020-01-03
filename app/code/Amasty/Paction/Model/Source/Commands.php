<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Source;

class Commands implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Amasty\Paction\Helper\Data
     */
    protected $_helper;

    protected $_types = [
        '',
        'addcategory',
        'removecategory',
        'replacecategory',
        '',
        'modifycost',
        'modifyprice',
        'modifyspecial',
        'modifyallprices',
        'updateadvancedprices',
        'addspecial',
        'addprice',
        'addspecialbycost',
        '',
        'relate',
        'upsell',
        'crosssell',
        '',
        'unrelate',
        'unupsell',
        'uncrosssell',
        '',
        'copyrelate',
        'copyupsell',
        'copycrosssell',
        '',
        'copyoptions',
        'copyattr',
        'copyimg',
        'removeimg',
        '',
        'changeattributeset',
        'changevisibility',
        '',
        'amdelete',
        '',
        'appendtext',
        'replacetext',
        ''
    ];

    public function __construct(
        \Amasty\Paction\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }

    public function toOptionArray()
    {
        $options = [];

        // magento wants at least one option to be selected
        $options[] = [
            'value' => '',
            'label' => '',

        ];

        foreach ($this->_types as $i => $type) {
            if ($type) {
                $data = $this->_helper->getActionDataByName($type);
                $options[] = [
                    'value' => $type,
                    'label' => __($data['label']),
                ];
            } else {
                $options[] = [
                    'value' => $i,
                    'label' => '---------------------',
                ];
            }
        }
        return $options;
    }
}