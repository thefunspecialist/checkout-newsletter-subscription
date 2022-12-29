<?php

namespace MageSuite\CheckoutNewsletterSubscription\Test\Unit\Plugin\Magento\Checkout\Api;

abstract class AbstractNewsletterSubscribeTest extends \PHPUnit\Framework\TestCase
{
    const CUSTOMER_EMAIL = 'customer@example.com';
    const CUSTOMER_ID = 5;
    const CART_ID = 10;

    /**
     * @var \Magento\Checkout\Api\PaymentInformationManagementInterface
     */
    protected ?\PHPUnit\Framework\MockObject\MockObject $subjectMock;

    /**
     * @var \Magento\Quote\Api\Data\PaymentInterface
     */
    protected ?\PHPUnit\Framework\MockObject\MockObject $paymentMethodMock;

    /**
     * @var \Magento\Quote\Api\Data\AddressInterface
     */
    protected ?\PHPUnit\Framework\MockObject\MockObject $billingAddressMock;

    /**
     * @var \Magento\Quote\Api\Data\AddressExtensionInterface
     */
    protected ?\PHPUnit\Framework\MockObject\MockObject $extensionAttributesMock;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected ?\PHPUnit\Framework\MockObject\MockObject $storeMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected ?\PHPUnit\Framework\MockObject\MockObject $storeManagerMock;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected ?\PHPUnit\Framework\MockObject\MockObject $quoteMock;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected ?\PHPUnit\Framework\MockObject\MockObject $quoteRepositoryMock;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected ?\PHPUnit\Framework\MockObject\MockObject $customerRepository;

    /**
     * @var \Magento\Newsletter\Model\SubscriptionManagerInterface
     */
    protected ?\PHPUnit\Framework\MockObject\MockObject $subscriptionManagerMock;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected ?\PHPUnit\Framework\MockObject\MockObject $loggerMock;

    protected function setUp(): void
    {
        $this->paymentMethodMock = $this->getMockBuilder(\Magento\Quote\Api\Data\PaymentInterface::class)->getMock();
        $this->extensionAttributesMock = $this->getMockBuilder(\Magento\Quote\Api\Data\AddressExtensionInterface::class)->getMock();

        $this->billingAddressMock = $this->getMockBuilder(\Magento\Quote\Api\Data\AddressInterface::class)->getMock();
        $this->billingAddressMock->method('getExtensionAttributes')->willReturn($this->extensionAttributesMock);

        $this->storeMock = $this->getMockBuilder(\Magento\Store\Model\Store::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();
        $this->storeMock->method('getId')->willReturn(\Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $this->storeManagerMock = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)->getMock();
        $this->storeManagerMock->method('getStore')->willReturn($this->storeMock);

        $this->quoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCustomerId'])
            ->getMock();
        $this->quoteMock->method('getCustomerId')->willReturn(self::CUSTOMER_ID);

        $this->quoteRepositoryMock = $this->getMockBuilder(\Magento\Quote\Api\CartRepositoryInterface::class)->getMock();
        $this->quoteRepositoryMock->method('get')->willReturn($this->quoteMock);

        $this->customerRepository = $this->getMockBuilder(\Magento\Customer\Api\CustomerRepositoryInterface::class)->getMock();
        $this->customerRepository->method('get')->willThrowException(new \Magento\Framework\Exception\NoSuchEntityException());

        $this->subscriptionManagerMock = $this->getMockBuilder(\Magento\Newsletter\Model\SubscriptionManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
    }

    /**
     * [orderId, isBillingAddressNull, newsletterSubscribe, shouldCallSubscribe]
     */
    public function pluginParamsProvider(): array
    {
        return [
            [1, true, true, false],
            [2, true, false, false],
            [3, false, true, true],
            [4, false, false, false]
        ];
    }
}
