define([
    'jquery',
    'mage/utils/wrapper',
    'MageSuite_CheckoutNewsletterSubscription/js/model/assigner'
], function ($, wrapper, assigner) {
    'use strict';

    return function (placeOrderAction) {
        return wrapper.wrap(placeOrderAction, function (originalAction, messageContainer, paymentData) {
            assigner(paymentData);

            return originalAction(messageContainer, paymentData);
        });
    };
});
