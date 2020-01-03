<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachmentApi
 */


namespace Amasty\ProductAttachmentApi\Model\ParamsOverrider;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Webapi\Rest\Request\ParamOverriderInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Replaces a "%amasty_customer_group%" value with the current authenticated customer's group id
 */
class AmastyCustomerGroup implements ParamOverriderInterface
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    public function __construct(
        UserContextInterface $userContext,
        CustomerRepository $customerRepository
    ) {
        $this->userContext = $userContext;
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function getOverriddenValue()
    {
        try {
            if ($this->userContext->getUserType() === UserContextInterface::USER_TYPE_CUSTOMER) {
                $customerId = $this->userContext->getUserId();

                /** @var \Magento\Customer\Api\Data\CustomerInterface */
                $user = $this->customerRepository->getById($customerId);

                if ($user) {
                    return $user->getGroupId();
                }
            }
        } catch (NoSuchEntityException $e) {
            /* do nothing and just return null */
        }
        return 0;
    }
}
