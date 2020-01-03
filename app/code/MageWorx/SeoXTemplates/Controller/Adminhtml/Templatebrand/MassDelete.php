<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatebrand;

use MageWorx\SeoXTemplates\Model\Template\Brand as TemplateBrandModel;

class MassDelete extends MassAction
{
    /**
     * @var string
     */
    protected $successMessage = 'A total of %1 record(s) have been deleted';

    /**
     * @var string
     */
    protected $errorMessage = 'An error occurred while deleting record(s).';

    /**
     *
     * @param TemplateBrandModel $template
     * @return $this
     */
    protected function doTheAction(TemplateBrandModel $template)
    {
        $template->delete();

        return $this;
    }
}
