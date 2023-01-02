<?php
declare(strict_types=1);

namespace MageSuite\CheckoutNewsletterSubscription\Model\Command;

class AssignNewsletterFlag
{
    public const NEWSLETTER_FLAG = 'newsletter_subscribe';

    protected \Magento\Framework\Serialize\SerializerInterface $serializer;

    public function __construct(\Magento\Framework\Serialize\SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function execute(
        \Magento\Quote\Api\Data\CartInterface $quote,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
    ): void {
        $newsletterFlag = null;

        if ($paymentMethod->getExtensionAttributes() && $paymentMethod->getExtensionAttributes()->getNewsletterSubscribe()) {
            $newsletterFlag = 1;
        }

        $quote->setData(self::NEWSLETTER_FLAG, $newsletterFlag);
    }
}
