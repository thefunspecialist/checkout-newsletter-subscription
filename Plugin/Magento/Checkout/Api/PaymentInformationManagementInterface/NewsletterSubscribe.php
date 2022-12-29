<?php

namespace MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api\PaymentInformationManagementInterface;

class NewsletterSubscribe
{
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;

    protected \Magento\Quote\Api\CartRepositoryInterface $quoteRepository;

    protected \Magento\Newsletter\Model\SubscriptionManagerInterface $subscriptionManager;

    protected \Psr\Log\LoggerInterface $logger;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Newsletter\Model\SubscriptionManagerInterface $subscriptionManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->storeManager = $storeManager;
        $this->quoteRepository = $quoteRepository;
        $this->subscriptionManager = $subscriptionManager;
        $this->logger = $logger;
    }

    public function afterSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Api\PaymentInformationManagementInterface $subject,
        $return,
        $cartId,
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
            $quote = $this->quoteRepository->get($cartId);
            $this->subscriptionManager->subscribeCustomer($quote->getCustomerId(), $this->storeManager->getStore()->getId());
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
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
}
