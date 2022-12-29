<?php
declare(strict_types=1);
\Magento\TestFramework\Workaround\Override\Fixture\Resolver::getInstance()
    ->requireDataFixture('Magento/Catalog/_files/products_new.php');
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var $product \Magento\Catalog\Model\Product */
$productRepository = $objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
$product = $productRepository->get('simple');
$addressData = include __DIR__ . '/../../../../../../dev/tests/integration/testsuite/Magento/Sales/_files/address_data.php';
/** @var $cart \Magento\Checkout\Model\Cart */
$cart = $objectManager->create(\Magento\Checkout\Model\Cart::class);
$cart->addProduct($product->getId(), ['qty' => 1]);
$quote = $cart->getQuote();
$quote->setInventoryProcessed(false);
$quote->getBillingAddress()->addData($addressData);
$quote->getShippingAddress()->addData($addressData);
$quote->setCustomerEmail($addressData['email']);
$shippingAddress = $quote->getShippingAddress();
$shippingAddress->setCollectShippingRates(true)
    ->collectShippingRates()
    ->setShippingMethod('flatrate_flatrate');
$quote->setPaymentMethod('checkmo');
$quote->setReservedOrderId('test_cart_with_simple_product');
$quote->setData(\MageSuite\CheckoutNewsletterSubscription\Model\Command\AssignNewsletterFlag::NEWSLETTER_FLAG, 1);
$quote->save();
$quote->getPayment()->importData(['method' => 'checkmo']);
$quote->collectTotals()->save();

$objectManager->removeSharedInstance(\Magento\Checkout\Model\Session::class);

/** @var \Magento\Quote\Model\QuoteIdMask $quoteIdMask */
$quoteIdMask = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create(\Magento\Quote\Model\QuoteIdMaskFactory::class)
    ->create();
$quoteIdMask->setQuoteId($cart->getQuote()->getId());
$quoteIdMask->setDataChanges(true);
$quoteIdMask->save();
