<?php

namespace MageSuite\CheckoutNewsletterSubscription\Test\Unit\Plugin\Magento\Checkout\Api\PaymentInformationManagementInterface;

class NewsletterSubscribeTest extends \PHPUnit\Framework\TestCase
{
    const CUSTOMER_EMAIL = 'customer@example.com';
    const CART_ID = 10;

    /**
     * @var \MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api\PaymentInformationManagementInterface\NewsletterSubscribe
     */
    protected $instance;

    /**
     * @var \Magento\Newsletter\Model\Subscriber|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $subscriberMock;

    /**
     * @var \Magento\Checkout\Api\PaymentInformationManagementInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $pluginSubjectMock;

    /**
     * @var \Magento\Quote\Api\Data\PaymentInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $paymentMethodMock;

    /**
     * @var \Magento\Quote\Api\Data\AddressInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $billingAddressMock;

    /**
     * @var \Magento\Quote\Api\Data\AddressExtensionInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $extensionAttributesMock;

    protected function setUp(): void
    {
        /** @var \Magento\Quote\Model\Quote|\PHPUnit\Framework\MockObject\MockObject $quoteMock */
        $quoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCustomerEmail'])
            ->getMock();
        $quoteMock->method('getCustomerEmail')->willReturn(self::CUSTOMER_EMAIL);

        /** @var \Magento\Quote\Api\CartRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject $quoteRepositoryMock */
        $quoteRepositoryMock = $this->getMockBuilder(\Magento\Quote\Api\CartRepositoryInterface::class)->getMock();
        $quoteRepositoryMock->method('get')->willReturn($quoteMock);

        $this->subscriberMock = $this->getMockBuilder(\Magento\Newsletter\Model\Subscriber::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject $loggerMock */
        $loggerMock = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();

        $this->instance = new \MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api\PaymentInformationManagementInterface\NewsletterSubscribe(
            $quoteRepositoryMock,
            $this->subscriberMock,
            $loggerMock
        );

        $this->pluginSubjectMock = $this->getMockBuilder(\Magento\Checkout\Api\PaymentInformationManagementInterface::class)->getMock();
        $this->paymentMethodMock = $this->getMockBuilder(\Magento\Quote\Api\Data\PaymentInterface::class)->getMock();
        $this->extensionAttributesMock = $this->getMockBuilder(\Magento\Quote\Api\Data\AddressExtensionInterface::class)->getMock();
        $this->billingAddressMock = $this->getMockBuilder(\Magento\Quote\Api\Data\AddressInterface::class)->getMock();
        $this->billingAddressMock->method('getExtensionAttributes')->willReturn($this->extensionAttributesMock);
    }

    public function testPluginCanBeInstantiated()
    {
        $this->assertInstanceOf(\MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api\PaymentInformationManagementInterface\NewsletterSubscribe::class, $this->instance);
    }

    /**
     * @dataProvider pluginParamsProvider
     */
    public function testAroundSavePaymentInformationAndPlaceOrderMethodReturnsResultOfProceedClosure($orderId, $isBillingAddressNull, $newsletterSubscribe)
    {
        $this->prepareBillingAddressExtensionAttributesMockForAroundMethodCall($newsletterSubscribe);

        $this->assertSame($orderId, $this->instance->afterSavePaymentInformationAndPlaceOrder(
            $this->pluginSubjectMock,
            $orderId,
            self::CART_ID,
            $this->paymentMethodMock,
            $isBillingAddressNull ? null : $this->billingAddressMock
        ));
    }

    /**
     * @dataProvider pluginParamsProvider
     */
    public function testAroundSavePaymentInformationAndPlaceOrderMethodCallsSubscribe($orderId, $isBillingAddressNull, $newsletterSubscribe, $shouldCallSubscribe) //phpcs:ignore
    {
        $this->prepareBillingAddressExtensionAttributesMockForAroundMethodCall($newsletterSubscribe);

        $this->subscriberMock->expects($shouldCallSubscribe ? $this->once() : $this->never())
            ->method('subscribe')
            ->with($this->equalTo(self::CUSTOMER_EMAIL));

        $this->instance->afterSavePaymentInformationAndPlaceOrder(
            $this->pluginSubjectMock,
            $orderId,
            self::CART_ID,
            $this->paymentMethodMock,
            $isBillingAddressNull ? null : $this->billingAddressMock
        );
    }

    protected function prepareBillingAddressExtensionAttributesMockForAroundMethodCall($newsletterSubscribe)
    {
        $this->extensionAttributesMock->method('getNewsletterSubscribe')->willReturn($newsletterSubscribe);
    }

    /**
     * Returns dataset for tests in the following row format
     * [orderId, isBillingAddressNull, newsletterSubscribe, shouldCallSubscribe]
     *
     * @return array
     */
    public function pluginParamsProvider()
    {
        return [
            [1, true, true, false],
            [2, true, false, false],
            [3, false, true, true],
            [4, false, false, false]
        ];
    }
}
