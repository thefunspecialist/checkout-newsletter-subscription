<?php

namespace MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api;

class GuestPaymentInformationManagementInterface
{
    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    private $subscriber;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Newsletter\Model\Subscriber $subscriber,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->subscriber = $subscriber;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject
     * @param \Closure $proceed
     * @param string $cartId
     * @param string $email
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @return int
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject,
        \Closure $proceed,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    )
    {
        $orderId = $proceed($cartId, $email, $paymentMethod, $billingAddress);
        if (null !== $billingAddress) {
            try {
                if ($billingAddress->getExtensionAttributes()->getNewsletterSubscribe()) {
                    $this->subscriber->subscribe($email);
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return $orderId;
    }
}
