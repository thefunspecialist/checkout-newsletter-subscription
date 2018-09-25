<?php

namespace MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api;

class PaymentInformationManagementInterface
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    private $subscriber;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Newsletter\Model\Subscriber $subscriber,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->subscriber = $subscriber;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Checkout\Api\PaymentInformationManagementInterface $subject
     * @param \Closure $proceed
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @return int
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Api\PaymentInformationManagementInterface $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    )
    {
        $orderId = $proceed($cartId, $paymentMethod, $billingAddress);
        if (null !== $billingAddress) {
            try {
                if ($billingAddress->getExtensionAttributes()->getNewsletterSubscribe()) {
                    $quote = $this->quoteRepository->get($cartId);
                    $this->subscriber->subscribe($quote->getCustomerEmail());
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return $orderId;
    }
}
