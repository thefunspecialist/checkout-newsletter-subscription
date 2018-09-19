<?php

namespace MageSuite\CheckoutNewsletterSubscription\Test\Unit\Plugin\Magento\Checkout\Api;

class PaymentInformationManagementInterfaceTest extends \PHPUnit\Framework\TestCase
{
    const CUSTOMER_EMAIL = 'customer@example.com';

    const CART_ID = 10;

    /**
     * @var \MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api\PaymentInformationManagementInterface
     */
    private $instance;

    /**
     * @var \Magento\Newsletter\Model\Subscriber|\PHPUnit_Framework_MockObject_MockObject
     */
    private $subscriberMock;

    /**
     * @var \Magento\Checkout\Api\PaymentInformationManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $pluginSubjectMock;

    /**
     * @var \Magento\Quote\Api\Data\PaymentInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $paymentMethodMock;

    /**
     * @var \Magento\Quote\Api\Data\AddressInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $billingAddressMock;

    /**
     * @var \Magento\Quote\Api\Data\AddressExtensionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesMock;

    protected function setUp()
    {
        /** @var \Magento\Quote\Model\Quote|\PHPUnit_Framework_MockObject_MockObject $quoteMock */
        $quoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCustomerEmail'])
            ->getMock();

        $quoteMock->method('getCustomerEmail')->willReturn(self::CUSTOMER_EMAIL);

        /** @var \Magento\Quote\Api\CartRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject $quoteRepositoryMock */
        $quoteRepositoryMock = $this->getMockBuilder(\Magento\Quote\Api\CartRepositoryInterface::class)
            ->getMock();

        $quoteRepositoryMock->method('get')->willReturn($quoteMock);

        $this->subscriberMock = $this->getMockBuilder(\Magento\Newsletter\Model\Subscriber::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $loggerMock */
        $loggerMock = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)
            ->getMock();

        $this->instance = new \MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api\PaymentInformationManagementInterface(
            $quoteRepositoryMock,
            $this->subscriberMock,
            $loggerMock
        );

        $this->pluginSubjectMock = $this->getMockBuilder(\Magento\Checkout\Api\PaymentInformationManagementInterface::class)
            ->getMock();

        $this->paymentMethodMock = $this->getMockBuilder(\Magento\Quote\Api\Data\PaymentInterface::class)
            ->getMock();

        $this->extensionAttributesMock = $this->getMockBuilder(\Magento\Quote\Api\Data\AddressExtensionInterface::class)
            ->getMock();

        $this->billingAddressMock = $this->getMockBuilder(\Magento\Quote\Api\Data\AddressInterface::class)
            ->getMock();

        $this->billingAddressMock->method('getExtensionAttributes')->willReturn($this->extensionAttributesMock);
    }

    /**
     * @param int $expectedOrderId
     * @return \Closure
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function getProceedClosureStub($expectedOrderId)
    {
        return function ($cartId, $paymentMethod, $billingAddress = null) use ($expectedOrderId) {
            return $expectedOrderId;
        };
    }

    /**
     * @param bool $newsletterSubscribe
     */
    private function prepareBillingAddressExtensionAttributesMockForAroundMethodCall($newsletterSubscribe)
    {
        $this->extensionAttributesMock->method('getNewsletterSubscribe')->willReturn($newsletterSubscribe);
    }

    public function testPluginCanBeInstantiated()
    {
        $this->assertInstanceOf(\MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api\PaymentInformationManagementInterface::class, $this->instance);
    }

    /**
     * @param int $orderId
     * @param bool $isBillingAddressNull
     * @param bool $newsletterSubscribe
     * @dataProvider pluginParamsProvider
     */
    public function testAroundSavePaymentInformationAndPlaceOrderMethodReturnsResultOfProceedClosure(
        $orderId,
        $isBillingAddressNull,
        $newsletterSubscribe
    )
    {
        $this->prepareBillingAddressExtensionAttributesMockForAroundMethodCall($newsletterSubscribe);

        $this->assertSame($orderId, $this->instance->aroundSavePaymentInformationAndPlaceOrder(
            $this->pluginSubjectMock,
            $this->getProceedClosureStub($orderId),
            self::CART_ID,
            $this->paymentMethodMock,
            $isBillingAddressNull ? null : $this->billingAddressMock
        ));
    }

    /**
     * @param int $orderId
     * @param bool $isBillingAddressNull
     * @param bool $newsletterSubscribe
     * @param bool $shouldCallSubscribe
     * @dataProvider pluginParamsProvider
     */
    public function testAroundSavePaymentInformationAndPlaceOrderMethodCallsSubscribe(
        $orderId,
        $isBillingAddressNull,
        $newsletterSubscribe,
        $shouldCallSubscribe
    )
    {
        $this->prepareBillingAddressExtensionAttributesMockForAroundMethodCall($newsletterSubscribe);

        $this->subscriberMock->expects($shouldCallSubscribe ? $this->once() : $this->never())
            ->method('subscribe')
            ->with($this->equalTo(self::CUSTOMER_EMAIL));

        $this->instance->aroundSavePaymentInformationAndPlaceOrder(
            $this->pluginSubjectMock,
            $this->getProceedClosureStub($orderId),
            self::CART_ID,
            $this->paymentMethodMock,
            $isBillingAddressNull ? null : $this->billingAddressMock
        );
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
