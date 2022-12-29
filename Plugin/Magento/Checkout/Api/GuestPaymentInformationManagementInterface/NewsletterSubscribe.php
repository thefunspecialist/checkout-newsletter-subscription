<?php

namespace MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api\GuestPaymentInformationManagementInterface;

class NewsletterSubscribe
{
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;

    protected \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository;

    protected \Magento\Newsletter\Model\SubscriptionManagerInterface $subscriptionManager;

    protected \Psr\Log\LoggerInterface $logger;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Newsletter\Model\SubscriptionManagerInterface $subscriptionManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->subscriptionManager = $subscriptionManager;
        $this->logger = $logger;
    }

    public function afterSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject,
        $return,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if ($billingAddress === null) {
            return $return;
        }

        if (!$this->isNewsletterCheckboxInCheckoutMarked($billingAddress)) {
            return $return;
        }

        try {
            $customerId = $this->getCustomerIdByEmail($email);
            if ($customerId) {
                $this->subscriptionManager->subscribeCustomer($customerId, $this->storeManager->getStore()->getId());
            } else {
                $this->subscriptionManager->subscribe($email, $this->storeManager->getStore()->getId());
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $return;
    }

    protected function isNewsletterCheckboxInCheckoutMarked(?\Magento\Quote\Api\Data\AddressInterface $billingAddress): bool
    {
        try {
            return (bool)$billingAddress->getExtensionAttributes()->getNewsletterSubscribe();
        } catch (\Exception $exception) {
            return false;
        }
    }

    protected function getCustomerIdByEmail(string $email): ?int
    {
        try {
            $customer = $this->customerRepository->get($email);

            return $customer->getId();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return null;
        }
    }
}
