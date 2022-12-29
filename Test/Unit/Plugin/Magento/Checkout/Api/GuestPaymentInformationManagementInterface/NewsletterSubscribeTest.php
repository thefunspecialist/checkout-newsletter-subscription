<?php

namespace MageSuite\CheckoutNewsletterSubscription\Test\Unit\Plugin\Magento\Checkout\Api\GuestPaymentInformationManagementInterface;

class NewsletterSubscribeTest extends \MageSuite\CheckoutNewsletterSubscription\Test\Unit\Plugin\Magento\Checkout\Api\AbstractNewsletterSubscribeTest
{
    /**
     * @var \Magento\Checkout\Api\GuestPaymentInformationManagementInterface
     */
    protected ?\PHPUnit\Framework\MockObject\MockObject $subjectMock;

    protected ?\MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api\GuestPaymentInformationManagementInterface\NewsletterSubscribe $plugin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subjectMock = $this->getMockBuilder(\Magento\Checkout\Api\GuestPaymentInformationManagementInterface::class)->getMock();

        $this->plugin = new \MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api\GuestPaymentInformationManagementInterface\NewsletterSubscribe(
            $this->storeManagerMock,
            $this->customerRepository,
            $this->subscriptionManagerMock,
            $this->loggerMock
        );
    }

    /**
     * @dataProvider pluginParamsProvider
     */
    public function testAfterSavePaymentInformationAndPlaceOrderMethodCallsSubscribe($orderId, $isBillingAddressNull, $newsletterSubscribe, $shouldCallSubscribe) //phpcs:ignore
    {
        $this->extensionAttributesMock->method('getNewsletterSubscribe')->willReturn($newsletterSubscribe);

        $this->subscriptionManagerMock->expects($shouldCallSubscribe ? $this->once() : $this->never())
            ->method('subscribe')
            ->with($this->equalTo(self::CUSTOMER_EMAIL), \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $this->plugin->afterSavePaymentInformationAndPlaceOrder(
            $this->subjectMock,
            $orderId,
            self::CART_ID,
            self::CUSTOMER_EMAIL,
            $this->paymentMethodMock,
            $isBillingAddressNull ? null : $this->billingAddressMock
        );
    }
}
