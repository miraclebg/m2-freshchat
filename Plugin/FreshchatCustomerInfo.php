<?php
/**
 * Copyright © Nimasystems (info@nimasystems.com). All rights reserved.
 * Please visit Nimasystems.com for license details
 */

declare(strict_types=1);

namespace Nimasystems\Freshchat\Plugin;

use Exception;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\CustomerData\Customer;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\Session;
use Nimasystems\Freshchat\Setup\Patch\Data\AddCustomerFreshchatRestoreIdAttr;

class FreshchatCustomerInfo
{
    /**
     * @var CurrentCustomer
     */
    protected CurrentCustomer $currentCustomer;

    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var AddressRepositoryInterface
     */
    protected AddressRepositoryInterface $addressRepository;

    public function __construct(
        CurrentCustomer            $currentCustomer,
        Session                    $session,
        AddressRepositoryInterface $addressRepository
    )
    {
        $this->currentCustomer = $currentCustomer;
        $this->customerSession = $session;
        $this->addressRepository = $addressRepository;
    }

    public function afterGetSectionData(Customer $subject, $result)
    {
        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->currentCustomer->getCustomer();

            $billingAddressId = $customer->getDefaultBilling();

            $telephone = null;

            //get default billing address
            try {
                $billingAddress = $this->addressRepository->getById($billingAddressId);
                $telephone = $billingAddress->getTelephone();
            } catch (Exception $e) {
                //
            }

            $result['uid'] = $customer->getId();
            $result['email'] = $customer->getEmail();
            $result['phone'] = $telephone;
            $result['firstname'] = $customer->getFirstname();
            $result['lastname'] = $customer->getLastname();
            $result['fullname'] = $customer->getFirstname() . ' ' . $customer->getLastname();

            $attr = $customer->getCustomAttribute(AddCustomerFreshchatRestoreIdAttr::FRESHCHAT_ATTRIBUTE_ID);
            $result['freshchatRestoreId'] = $attr ? $attr->getValue() : null;
        }

        return $result;
    }
}
