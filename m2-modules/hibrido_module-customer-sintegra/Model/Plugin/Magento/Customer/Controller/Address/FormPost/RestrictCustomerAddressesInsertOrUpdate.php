<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\CustomerSintegra\Model\Plugin\Magento\Customer\Controller\Address\FormPost;

use Hibrido\CustomerBR\Model\Source\Config\PersonType;
use Magento\Customer\Controller\Address\FormPost;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Message\ManagerInterface as MessageManager;

class RestrictCustomerAddressesInsertOrUpdate
{
    /**
     * @var Http
     */
    private $response;

    /**
     * @var RedirectInterface
     */
    private $redirect;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @param Http $response
     * @param RedirectInterface $redirect
     * @param MessageManager $messageManager
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Http $response,
        RedirectInterface $redirect,
        MessageManager $messageManager,
        CustomerSession $customerSession
    ) {
        $this->response = $response;
        $this->redirect = $redirect;
        $this->messageManager = $messageManager;
        $this->customerSession = $customerSession;
    }


    /**
     * Dispatch actions allowed for not authorized users
     *
     * @param FormPost $subject
     * @param RequestInterface $request
     * @return void
     */
    public function beforeDispatch(FormPost $subject, RequestInterface $request): void
    {
        //If we can't get customer from session bail out.
        if (!$customer = $this->customerSession->getCustomer()) {
            return;
        }

        //If customer is not legal person or we can't identify person type bail out.
        if (!$this->isCustomerLegalPerson($customer)) {
            return;
        }

        //Customer is fine so we can continue ...

        //Set controller action as dispatched.
        $subject->getActionFlag()->set('', ActionInterface::FLAG_NO_DISPATCH, true);

        //Add some info message to the user.
        $this->messageManager->addNoticeMessage(__('You can\'t edit your addresses if you are a Legal Person.'));

        //Redirect the user to customer addresses list.
        $this->redirect->redirect($this->response, '*/*/index');
    }

    /**
     * @param Customer $customer
     * @return bool
     * @noinspection PhpUndefinedMethodInspection
     */
    private function isCustomerLegalPerson(Customer $customer): bool
    {
        if (!$customer->hasHbPersonType() || $customer->getTaxvat() == '') {
            return false;
        }

        if ($customer->getHbPersonType() !== PersonType::PERSON_TYPE_LEGAL) {
            return false;
        }

        return true;
    }

}