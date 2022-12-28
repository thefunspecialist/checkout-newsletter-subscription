<?php
declare(strict_types=1);

namespace MageSuite\CheckoutNewsletterSubscription\Model\Command;

class SubscribeByQuote
{
    protected \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository;

    protected \Magento\Newsletter\Model\SubscriptionManagerInterface $subscriptionManager;

    protected \Psr\Log\LoggerInterface $logger;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Newsletter\Model\SubscriptionManagerInterface $subscriptionManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->subscriptionManager = $subscriptionManager;
        $this->logger = $logger;
    }

    public function execute(\Magento\Quote\Model\Quote $quote): void
    {
        if ($quote->getData(\MageSuite\CheckoutNewsletterSubscription\Model\Command\AssignNewsletterFlag::NEWSLETTER_FLAG) != 1) {
            return;
        }

        $email = $quote->getCustomerEmail();

        try {
            $customerId = $this->getCustomerIdByEmail($email);

            if ($customerId) {
                $this->subscriptionManager->subscribeCustomer($customerId, $quote->getStoreId());
                return;
            }

            $this->subscriptionManager->subscribe($email, $quote->getStoreId());
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    protected function getCustomerIdByEmail(string $email): ?int
    {
        try {
            $customer = $this->customerRepository->get($email);

            return (int)$customer->getId();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return null;
        }
    }
}
