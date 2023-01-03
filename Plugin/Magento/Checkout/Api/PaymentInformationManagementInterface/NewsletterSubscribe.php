<?php
declare(strict_types=1);

namespace MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api\PaymentInformationManagementInterface;

class NewsletterSubscribe
{
    protected \MageSuite\CheckoutNewsletterSubscription\Model\Command\AssignNewsletterFlag $assignNewsletterFlag;

    protected \Magento\Quote\Api\CartRepositoryInterface $cartRepository;

    public function __construct(
        \MageSuite\CheckoutNewsletterSubscription\Model\Command\AssignNewsletterFlag $assignNewsletterFlag,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository
    ) {
        $this->assignNewsletterFlag = $assignNewsletterFlag;
        $this->cartRepository = $cartRepository;
    }

    public function beforeSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Api\PaymentInformationManagementInterface $subject,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $quote = $this->getQuote($cartId);
        $this->assignNewsletterFlag->execute($quote, $paymentMethod);
    }

    public function beforeSavePaymentInformation(
        \Magento\Checkout\Api\PaymentInformationManagementInterface $subject,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $quote = $this->getQuote($cartId);
        $this->assignNewsletterFlag->execute($quote, $paymentMethod);
    }

    protected function getQuote(int $cartId): \Magento\Quote\Api\Data\CartInterface
    {
        return $this->cartRepository->getActive($cartId);
    }
}
