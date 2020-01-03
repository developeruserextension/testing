<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


namespace Amasty\StoreCredit\Api\Data;

interface SalesFieldInterface
{
    const AMSC_USE = 'amstorecredit_use';
    const AMSC_BASE_AMOUNT = 'amstorecredit_base_amount';
    const AMSC_AMOUNT = 'amstorecredit_amount';
    const AMSC_INVOICED_BASE_AMOUNT = 'amstorecredit_invoiced_base_amount';
    const AMSC_INVOICED_AMOUNT = 'amstorecredit_invoiced_amount';
    const AMSC_REFUNDED_BASE_AMOUNT = 'amstorecredit_refunded_base_amount';
    const AMSC_REFUNDED_AMOUNT = 'amstorecredit_refunded_amount';
}
