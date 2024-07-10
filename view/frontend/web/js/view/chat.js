define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'mage/translate',
], function ($, customerData, $t) {
    'use strict'

    var FreshchatWidget = window['fcWidget'] || null;

    var isLoggedIn = function (customerInfo) {
        // noinspection RedundantConditionalExpressionJS
        return customerInfo && customerInfo.firstname ? true : false;
    };

    function configureWithUserContext(isUserAuthenticated, userData, context) {

        if (FreshchatWidget) {
            FreshchatWidget.setExternalId(userData.id);
            FreshchatWidget.user.setFirstName(userData.fullname);
            FreshchatWidget.user.setEmail(userData.email);
        }

        console.log('configureSentryContext', {
            isUserAuthenticated,
            userData,
            context
        });
    }

    function configureUserData() {
        var customerInfo = customerData.get('customer');
        var customer = customerInfo();

        if (!customer || !(customer.data_id || null)) {
            customerInfo.subscribe(function () {
                configureWithUserContext(isLoggedIn(customer), customer);
            }, this);
        } else {
            configureWithUserContext(isLoggedIn(customer), customer);
        }
    }

    return function (config) {
        configureUserData();
    }
});
