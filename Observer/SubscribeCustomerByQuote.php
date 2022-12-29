<?php
declare(strict_types=1);

namespace MageSuite\CheckoutNewsletterSubscription\Observer;

class SubscribeCustomerByQuote implements \Magento\Framework\Event\ObserverInterface
{
    protected \MageSuite\CheckoutNewsletterSubscription\Model\Command\SubscribeByQuote $subscribeByQuote;

    public function __construct(\MageSuite\CheckoutNewsletterSubscription\Model\Command\SubscribeByQuote $subscribeByQuote)
    {
        $this->subscribeByQuote = $subscribeByQuote;
    }

    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        $quote = $observer->getQuote();
        $this->subscribeByQuote->execute($quote);
    }
}
