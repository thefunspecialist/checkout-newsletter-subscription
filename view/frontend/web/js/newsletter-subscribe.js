define(
    [
        'ko',
        'uiComponent'
    ],
    function (ko, Component) {
        "use strict";
        return Component.extend({
            defaults: {
                displayArea: 'newsletter-subscribe',
                template: 'MageSuite_CheckoutNewsletterSubscription/newsletter-subscribe'
            },
            isChecked: ko.observable(false)
        });
    }
);
