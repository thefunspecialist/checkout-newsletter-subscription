<?php

namespace MageSuite\CheckoutNewsletterSubscription\Test\Unit\Plugin\Magento\Checkout\Api\PaymentInformationManagementInterface;

class NewsletterSubscribeTest extends \MageSuite\CheckoutNewsletterSubscription\Test\Unit\Plugin\Magento\Checkout\Api\AbstractNewsletterSubscribeTest
{
    /**
     * @var \Magento\Checkout\Api\PaymentInformationManagementInterface
     */
    protected ?\PHPUnit\Framework\MockObject\MockObject $subjectMock;

    protected ?\MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api\PaymentInformationManagementInterface\NewsletterSubscribe $plugin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subjectMock = $this->getMockBuilder(\Magento\Checkout\Api\PaymentInformationManagementInterface::class)->getMock();

        $this->plugin = new \MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api\PaymentInformationManagementInterface\NewsletterSubscribe(
            $this->storeManagerMock,
            $this->quoteRepositoryMock,
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
            ->method('subscribeCustomer')
            ->with($this->equalTo(self::CUSTOMER_ID), \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $this->plugin->afterSavePaymentInformationAndPlaceOrder(
            $this->subjectMock,
            $orderId,
            self::CART_ID,
            $this->paymentMethodMock,
            $isBillingAddressNull ? null : $this->billingAddressMock
        );
    }
}
