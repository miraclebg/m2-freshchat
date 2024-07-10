<?php
/**
 * Copyright © Nimasystems (info@nimasystems.com). All rights reserved.
 * Please visit Nimasystems.com for license details
 */

declare(strict_types=1);

namespace Nimasystems\Freshchat\Controller\Freshchat;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Nimasystems\Freshchat\Setup\Patch\Data\AddCustomerFreshchatRestoreIdAttr;

class Update extends Action
{
    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @var CurrentCustomer
     */
    protected CurrentCustomer $currentCustomer;

    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    protected CustomerRepositoryInterface $customerRepository;

    /**
     * City constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param CurrentCustomer $currentCustomer
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context                     $context,
        PageFactory                 $resultPageFactory,
        JsonFactory                 $resultJsonFactory,
        CurrentCustomer             $currentCustomer,
        Session                     $customerSession,
        CustomerRepositoryInterface $customerRepository
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->currentCustomer = $currentCustomer;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;

        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        try {
            if ($this->_request->isPost()) {
                $restoreId = (string)$this->_request->getPost()->get('restore_id');

                if (!$restoreId) {
                    throw new Exception('Invalid restore ID');
                }

                $authenticated = $this->customerSession->isLoggedIn();

                if (!$authenticated) {
                    throw new Exception('Not allowed');
                }

                $customer = $this->currentCustomer->getCustomer();

                $customerId = $customer->getId();
                $cst = $this->customerRepository->getById($customerId);
                $cst->setCustomAttribute(AddCustomerFreshchatRestoreIdAttr::FRESHCHAT_ATTRIBUTE_ID, $restoreId);
                $this->customerRepository->save($cst);

                return $result->setData(['success' => true]);
            }
        } catch (LocalizedException|\Exception $e) {
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }

        return $result;
    }


    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
