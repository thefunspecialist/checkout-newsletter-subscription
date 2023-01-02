define([
    'jquery',
    'mage/utils/wrapper',
    'MageSuite_CheckoutNewsletterSubscription/js/model/assigner'
], function ($, wrapper, assigner) {
    'use strict';

    return function (placeOrderAction) {
        return wrapper.wrap(placeOrderAction, function (originalAction, serviceUrl, payload, messageContainer) {
            assigner(payload['paymentMethod']);

            return originalAction(serviceUrl, payload, messageContainer);
        });
    };
});
