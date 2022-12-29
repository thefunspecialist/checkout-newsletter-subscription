<?php
declare(strict_types=1);

namespace MageSuite\CheckoutNewsletterSubscription\Test\Integration\Model\Command;

class SubscribeByQuoteTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder;

    protected ?\Magento\Quote\Api\CartRepositoryInterface $cartRepository;

    protected ?\Magento\Quote\Model\QuoteManagement $quoteManagement;

    protected ?\Magento\Newsletter\Model\GuestSubscriptionChecker $guestSubscriptionChecker;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->searchCriteriaBuilder = $objectManager->get(
            \Magento\Framework\Api\SearchCriteriaBuilder::class
        );
        $this->cartRepository = $objectManager->get(
            \Magento\Quote\Api\CartRepositoryInterface::class
        );
        $this->quoteManagement = $objectManager->get(
            \Magento\Quote\Model\QuoteManagement::class
        );
        $this->guestSubscriptionChecker = $objectManager->get(
            \Magento\Newsletter\Model\GuestSubscriptionChecker::class
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_CheckoutNewsletterSubscription::Test/Integration/_files/quote_with_newsletter_flag.php
     */
    public function testItAddsCustomerToNewsletterList()
    {
        $quote = $this->getQuote('test_cart_with_simple_product');
        $this->quoteManagement->submit($quote);
        $this->assertTrue($this->guestSubscriptionChecker->isSubscribed($quote->getCustomerEmail()));
    }

    protected function getQuote($reservedOrderId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('reserved_order_id', $reservedOrderId)
            ->create();
        /** @var \Magento\Quote\Api\CartRepositoryInterface $quoteRepository */
        $items = $this->cartRepository
            ->getList($searchCriteria)
            ->getItems();

        return array_pop($items);
    }
}
