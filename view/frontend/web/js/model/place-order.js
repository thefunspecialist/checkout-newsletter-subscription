define(
    [
        'jquery',
        'mage/utils/wrapper'
    ],
    function ($, wrapper) {
        'use strict';
        return function (newPlaceOrderModel) {
            return wrapper.wrap(newPlaceOrderModel, function (originalModel, serviceUrl, payload, messageContainer) {

                if (payload.billingAddress['extension_attributes'] === undefined) {
                    payload.billingAddress['extension_attributes'] = {};
                }
                payload.billingAddress['extension_attributes']['newsletter_subscribe'] = Boolean($('[name="checkout-newsletter-subscribe"]').attr('checked'));

                // pass execution to original model ('Magento_Checkout/js/model/place-order')
                return originalModel(serviceUrl, payload, messageContainer);
            });
        };
    }
);
