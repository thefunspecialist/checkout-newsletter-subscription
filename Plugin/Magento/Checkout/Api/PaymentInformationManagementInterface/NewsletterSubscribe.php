<?php

namespace MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api\PaymentInformationManagementInterface;

class NewsletterSubscribe
{
    protected \Magento\Quote\Api\CartRepositoryInterface $quoteRepository;

    protected \Magento\Newsletter\Model\Subscriber $subscriber;

    protected \Psr\Log\LoggerInterface $logger;

    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Newsletter\Model\Subscriber $subscriber,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->subscriber = $subscriber;
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

        try {
            if ($billingAddress->getExtensionAttributes()->getNewsletterSubscribe()) {
                $quote = $this->quoteRepository->get($cartId);
                $this->subscriber->subscribe($quote->getCustomerEmail());
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $return;
    }
}
