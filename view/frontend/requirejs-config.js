var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/place-order': {
                'MageSuite_CheckoutNewsletterSubscription/js/model/place-order-ext': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'MageSuite_CheckoutNewsletterSubscription/js/model/set-payment-information-ext': true
            }
        }
    }
};
