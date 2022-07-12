<?php

namespace MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api\GuestPaymentInformationManagementInterface;

class NewsletterSubscribe
{
    protected \Magento\Newsletter\Model\Subscriber $subscriber;

    protected \Psr\Log\LoggerInterface $logger;

    public function __construct(
        \Magento\Newsletter\Model\Subscriber $subscriber,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->subscriber = $subscriber;
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

        try {
            if ($billingAddress->getExtensionAttributes()->getNewsletterSubscribe()) {
                $this->subscriber->subscribe($email);
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $return;
    }
}
