<?php
declare(strict_types=1);

namespace MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api\GuestPaymentInformationManagementInterface;

class NewsletterSubscribe
{
    protected \MageSuite\CheckoutNewsletterSubscription\Model\Command\AssignNewsletterFlag $assignNewsletterFlag;

    protected \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory;

    protected \Magento\Quote\Api\CartRepositoryInterface $cartRepository;

    public function __construct(
        \MageSuite\CheckoutNewsletterSubscription\Model\Command\AssignNewsletterFlag $assignNewsletterFlag,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository
    ) {
        $this->assignNewsletterFlag = $assignNewsletterFlag;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartRepository = $cartRepository;
    }

    public function beforeSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $quote = $this->getQuote($cartId);
        $this->assignNewsletterFlag->execute($quote, $paymentMethod);
    }

    public function beforeSavePaymentInformation(
        \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $quote = $this->getQuote($cartId);
        $this->assignNewsletterFlag->execute($quote, $paymentMethod);
    }

    protected function getQuote(string $cartId): \Magento\Quote\Api\Data\CartInterface
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->cartRepository->get($quoteIdMask->getQuoteId());
    }
}
